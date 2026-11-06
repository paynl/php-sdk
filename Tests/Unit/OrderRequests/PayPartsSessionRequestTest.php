<?php

declare(strict_types=1);

namespace Tests\Unit\OrderRequests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Http\GuzzleClientFactoryInterface;
use PayNL\Sdk\Model\Address;
use PayNL\Sdk\Model\Amount;
use PayNL\Sdk\Model\Customer;
use PayNL\Sdk\Model\Order;
use PayNL\Sdk\Model\Product;
use PayNL\Sdk\Model\Request\PayPartsSessionCreateRequest;
use PayNL\Sdk\Model\Request\PayPartsSessionGetRequest;
use PHPUnit\Framework\TestCase;

final class PayPartsSessionRequestTest extends TestCase
{
    private array $history = [];

    private function config(array $responses): Config
    {
        $handler = HandlerStack::create(new MockHandler($responses));
        $handler->push(Middleware::history($this->history));
        $factory = $this->createMock(GuzzleClientFactoryInterface::class);
        $factory->expects($this->once())->method('create')->willReturnCallback(static function (array $options) use ($handler): Client {
            return new Client($options + ['handler' => $handler]);
        });
        return (new Config())->setUsername('SL-1234-5678')->setPassword('session-secret')
            ->setDebug(false)->setGuzzleClientFactory($factory);
    }

    private function createRequest(): PayPartsSessionCreateRequest
    {
        return (new PayPartsSessionCreateRequest())->setAmount(12.34)
            ->setReturnUrl('https://shop.example/return')->setExchangeUrl('https://shop.example/exchange');
    }

    public function testCreateSendsAuthenticatedSessionPayloadAndMapsResponse(): void
    {
        $config = $this->config([new Response(200, [], '{"sessionId":"abc123","sessionToken":"token123"}')]);
        $request = $this->createRequest()->setConfig($config);
        $address = (new Address())->setFirstName('Jane')->setLastName('Doe')
            ->setStreetName('Main Street')->setStreetNumber('1')->setZipCode('1234AB')
            ->setCity('Amsterdam')->setCountryCode('NL');
        $order = (new Order())->setCountryCode('NL')->setDeliveryAddress($address)->setInvoiceAddress($address)
            ->addProduct(new Product('SKU-1', 'Article', 12.34, 'EUR', Product::TYPE_ARTICLE, 1, null, 0.0));
        $request->setCustomer((new Customer())->setFirstName('Jane')->setEmail('jane@example.com')->setLocale('nl-NL'))
            ->setOrder($order)->setDescription('Test order')->setReference('REF-123')
            ->setExpire('2026-12-01T12:00:00Z')->setIntegration(['checkoutUrl' => 'https://shop.example/checkout']);

        $result = $request->start();

        self::assertSame('abc123', $result->getSessionId());
        self::assertSame('token123', $result->getSessionToken());
        $sent = $this->history[0]['request'];
        self::assertSame('POST', $sent->getMethod());
        self::assertSame('https://parts.pay.nl/v1/session/start', (string)$sent->getUri());
        self::assertSame('Basic ' . base64_encode('SL-1234-5678:session-secret'), $sent->getHeaderLine('Authorization'));
        self::assertSame('application/json', $sent->getHeaderLine('Content-Type'));
        $body = json_decode((string)$sent->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame(['value' => 1234, 'currency' => 'EUR'], $body['amount']);
        self::assertSame('Jane', $body['order']['deliveryAddress']['firstName']);
        self::assertSame('Main Street', $body['order']['invoiceAddress']['street']);
        self::assertSame('NL', $body['order']['invoiceAddress']['country']);
        self::assertSame(1234, $body['order']['products'][0]['price']['value']);
        self::assertSame(0, $body['order']['products'][0]['vatPercentage']);
        self::assertSame('nl-NL', $body['customer']['locale']);
        self::assertSame('Test order', $body['description']);
        self::assertSame('REF-123', $body['reference']);
        self::assertSame('2026-12-01T12:00:00Z', $body['expire']);
        self::assertSame(['checkoutUrl' => 'https://shop.example/checkout'], $body['integration']);
        self::assertArrayNotHasKey('serviceId', $body);
        self::assertSame('', $config->getCore());
        self::assertSame('', $config->getFailoverUrl());
    }

    public function testGetReturnsDetailsAndOrderStatusUsingZeroHost(): void
    {
        $data = [
            'sessionId' => 'abc123', 'sessionToken' => 'token123',
            'amount' => ['value' => 100, 'currency' => 'EUR'],
            'customer' => ['email' => 'jane@example.com'],
            'order' => ['products' => [['id' => 'SKU-1']]],
            'orderStatus' => ['orderId' => 'order123', 'status' => 'PENDING', 'isFullyPaid' => false,
                'payments' => [['status' => ['code' => 25, 'action' => 'AUTHORIZE']]]],
        ];
        $config = $this->config([new Response(200, [], json_encode($data))])
            ->setCore(Config::TGU3)->setVersion(3);
        $result = (new PayPartsSessionGetRequest())->setConfig($config)->setSessionId('abc123')->useZero()->start();

        self::assertSame('abc123', $result->getSessionId());
        self::assertSame('token123', $result->getSessionToken());
        foreach (['amount', 'customer', 'order', 'orderStatus'] as $field) {
            self::assertSame($data[$field], $result->{'get' . ucfirst($field)}());
        }
        $sent = $this->history[0]['request'];
        self::assertSame('GET', $sent->getMethod());
        self::assertSame('https://zero-parts.pay.nl/v1/session/abc123', (string)$sent->getUri());
        self::assertSame('', (string)$sent->getBody());
        self::assertSame('Basic ' . base64_encode('SL-1234-5678:session-secret'), $sent->getHeaderLine('Authorization'));
    }

    public function testEnvironmentSelectionIgnoresCoreSettingForBothRequests(): void
    {
        foreach ([false, true] as $zero) {
            foreach ([$this->createRequest(), (new PayPartsSessionGetRequest())->setSessionId('abc')] as $request) {
                $this->history = [];
                $config = $this->config([new Response(200, [], '{"sessionId":"abc","sessionToken":"token"}')])
                    ->setCore(Config::TGU1)->setVersion(3);
                $request->setConfig($config)->useZero();
                if (!$zero) {
                    $request->useZero(false);
                }
                $request->start();

                $uri = $this->history[0]['request']->getUri();
                self::assertSame($zero ? 'zero-parts.pay.nl' : 'parts.pay.nl', $uri->getHost());
                self::assertSame('/v1/' . ($request instanceof PayPartsSessionCreateRequest ? 'session/start' : 'session/abc'), $uri->getPath());
                self::assertSame(Config::TGU1, $config->getCore());
                self::assertSame('', $config->getFailoverUrl());
            }
        }
    }

    public function testExplicitFailoverOverridesBothEnvironmentsForBothRequests(): void
    {
        foreach ([false, true] as $zero) {
            foreach ([$this->createRequest(), (new PayPartsSessionGetRequest())->setSessionId('abc')] as $request) {
                $this->history = [];
                $config = $this->config([new Response(200, [], '{"sessionId":"abc","sessionToken":"token"}')])
                    ->setCore(Config::TGU1)->setupFailoverUrl('https://failover.example');
                $request->setConfig($config)->useZero($zero)->start();

                $uri = $this->history[0]['request']->getUri();
                self::assertSame('failover.example', $uri->getHost());
                self::assertSame('/v1/' . ($request instanceof PayPartsSessionCreateRequest ? 'session/start' : 'session/abc'), $uri->getPath());
                self::assertSame(Config::TGU1, $config->getCore());
                self::assertSame('https://failover.example', $config->getFailoverUrl());
            }
        }
    }

    public function testDefaultHostIgnoresZeroCoreSetting(): void
    {
        foreach ([$this->createRequest(), (new PayPartsSessionGetRequest())->setSessionId('abc')] as $request) {
            $this->history = [];
            $config = $this->config([new Response(200, [], '{"sessionId":"abc","sessionToken":"token"}')])
                ->setCore(PayPartsSessionGetRequest::ZERO_API_URL);
            $request->setConfig($config)->start();
            self::assertSame('parts.pay.nl', $this->history[0]['request']->getUri()->getHost());
        }
    }

    public function testGetBeforeOrderExists(): void
    {
        $config = $this->config([new Response(200, [], '{"sessionId":"abc","sessionToken":"token","orderStatus":null}')]);
        $result = (new PayPartsSessionGetRequest())->setConfig($config)->setSessionId('abc')->start();
        self::assertNull($result->getOrderStatus());
    }

    public function testGetWithoutOrderStatus(): void
    {
        $config = $this->config([new Response(200, [], '{"sessionId":"abc","sessionToken":"token"}')]);
        $result = (new PayPartsSessionGetRequest())->setConfig($config)->setSessionId('abc')->start();
        self::assertNull($result->getOrderStatus());
    }

    public function testApiErrorUsesSdkException(): void
    {
        $config = $this->config([new Response(404, [], '{"code":"PAY-4040","detail":"Session not found"}')]);
        $this->expectException(PayException::class);
        $this->expectExceptionMessage('PAY-4040 - Session not found');
        (new PayPartsSessionGetRequest())->setConfig($config)->setSessionId('missing')->start();
    }

    public function testIncompleteCreateResponseIsRejected(): void
    {
        $config = $this->config([new Response(200, [], '{"sessionId":"abc"}')]);
        $this->expectException(PayException::class);
        $this->expectExceptionMessage('missing session ID or token');
        $this->createRequest()->setConfig($config)->start();
    }

    public function testExplicitlyEmptyCredentialsAreRejected(): void
    {
        $request = $this->createRequest()->setConfig((new Config())->setUsername('')->setPassword(''));
        $this->expectException(PayException::class);
        $this->expectExceptionMessage('Please check your config');
        $request->start();
    }

    public function testBothRequestsInheritGlobalDebugSetting(): void
    {
        $globalConfig = require __DIR__ . '/../../../config/config.global.php';
        foreach ([new PayPartsSessionCreateRequest(), new PayPartsSessionGetRequest()] as $request) {
            $request->setConfig((new Config())->setUsername('SL-1234-5678')->setPassword('test-secret'));
            $property = new \ReflectionProperty($request, 'config');
            self::assertSame($globalConfig['debug'], $property->getValue($request)->get('debug'));
        }
    }

    public function testDebugOutputIncludesRawForbiddenResponse(): void
    {
        foreach ([$this->createRequest(), (new PayPartsSessionGetRequest())->setSessionId('abc')] as $request) {
            $config = $this->config([new Response(403, [], '{"message":"Forbidden"}')])->setDebug(true);
            $request->setConfig($config);
            ob_start();
            try {
                $request->start();
                self::fail('Expected a PayException for HTTP 403');
            } catch (PayException $exception) {
                self::assertSame(403, $exception->getCode());
            } finally {
                $output = ob_get_clean();
            }
            self::assertStringContainsString('Requested URL', $output);
            self::assertStringContainsString('Raw response body', $output);
            self::assertStringContainsString('Forbidden', $output);
        }
    }

    public function testMissingConfigIsRejected(): void
    {
        $this->expectException(PayException::class);
        $this->expectExceptionMessage('Please check your config');
        $this->createRequest()->start();
    }

    public function testAmountObjectContainsCents(): void
    {
        $body = $this->createRequest()->setAmount(new Amount(99, 'USD'))->getBodyParameters();
        self::assertSame(['value' => 99, 'currency' => 'USD'], $body['amount']);
    }

    public function testCurrencySetter(): void
    {
        self::assertSame('USD', $this->createRequest()->setCurrency('USD')->getBodyParameters()['amount']['currency']);
    }

    public function testZeroAmountIsRejected(): void
    {
        $this->expectException(PayException::class);
        $this->createRequest()->setAmount(0)->getBodyParameters();
    }

    public function testMissingReturnUrlIsRejected(): void
    {
        $this->expectException(PayException::class);
        $this->expectExceptionMessage('returnUrl');
        (new PayPartsSessionCreateRequest())->setAmount(1)->setExchangeUrl('https://shop.example/exchange')->getBodyParameters();
    }

    public function testMissingExchangeUrlIsRejected(): void
    {
        $this->expectException(PayException::class);
        $this->expectExceptionMessage('exchangeUrl');
        (new PayPartsSessionCreateRequest())->setAmount(1)->setReturnUrl('https://shop.example/return')->getBodyParameters();
    }

    public function testMissingSessionIdIsRejected(): void
    {
        $this->expectException(PayException::class);
        (new PayPartsSessionGetRequest())->setSessionId(' ')->getPathParameters();
    }

    public function testSessionIdIsEncodedAsOneSegment(): void
    {
        self::assertSame(['sessionId' => 'abc%2Fdef%3Ftest%3D1'],
            (new PayPartsSessionGetRequest())->setSessionId('abc/def?test=1')->getPathParameters());
    }
}
