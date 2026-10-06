<?php

declare(strict_types=1);

namespace Tests\Unit\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PayNL\Sdk\Request\Request;
use PayNL\Sdk\Response\Response;
use PHPUnit\Framework\TestCase;

class AbstractRequestExecuteTest extends TestCase
{
    public function testExecuteUsesClientBaseUriWithOverriddenApplyClient(): void
    {
        $mock = new MockHandler([
            new GuzzleResponse(200, ['Content-Type' => 'application/json'], '{"id":"1"}'),
        ]);
        $client = new Client([
            'handler' => HandlerStack::create($mock),
            'base_uri' => 'https://rest.pay.nl/v2/',
        ]);

        $request = new class ('orders', Request::METHOD_GET) extends Request {
            public function applyClient(Client $client): self
            {
                parent::applyClient($client);
                return $this;
            }
        };
        $request->applyClient($client);

        $response = new Response();
        $request->execute($response);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"id":"1"}', $response->getRawBody());

        $lastRequest = $mock->getLastRequest();
        $this->assertNotNull($lastRequest);
        $this->assertSame('GET', $lastRequest->getMethod());
        $this->assertSame('https://rest.pay.nl/v2/orders', (string)$lastRequest->getUri());
    }

    public function testExecuteUsesAbsoluteUriWhenUrlOptionIsSet(): void
    {
        $mock = new MockHandler([
            new GuzzleResponse(200, ['Content-Type' => 'application/json'], '{"ok":true}'),
        ]);
        $client = new Client([
            'handler' => HandlerStack::create($mock),
            'base_uri' => 'https://rest.pay.nl/v2/',
        ]);

        $request = new Request('orders', Request::METHOD_POST, [], ['url' => 'https://failover.pay.nl']);
        $request->applyClient($client);

        $response = new Response();
        $request->execute($response);

        $this->assertSame(200, $response->getStatusCode());

        $lastRequest = $mock->getLastRequest();
        $this->assertNotNull($lastRequest);
        $this->assertSame('POST', $lastRequest->getMethod());
        $this->assertSame('https://failover.pay.nl/orders', (string)$lastRequest->getUri());
    }

    public function testExecuteResolvesOverrideUrlWithoutTrailingSlash(): void
    {
        $mock = new MockHandler([new GuzzleResponse(200, [], '{}')]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $request = new Request('orders', Request::METHOD_GET, [], ['url' => 'https://example.com/custom']);
        $request->applyClient($client);
        $request->setDebug(true);

        ob_start();
        try {
            $request->execute(new Response());
            $debugOutput = (string)ob_get_contents();
        } finally {
            ob_end_clean();
        }

        $lastRequest = $mock->getLastRequest();
        $this->assertNotNull($lastRequest);
        $this->assertSame('https://example.com/orders', (string)$lastRequest->getUri());
        $this->assertStringContainsString('https://example.com/orders', $debugOutput);
        $this->assertStringNotContainsString('https://example.com/custom/', $debugOutput);
    }

    public function testExecutePreservesHttpErrorResponse(): void
    {
        $rawBody = '{"message":"Payment rejected"}';
        $mock = new MockHandler([new GuzzleResponse(422, [], $rawBody)]);
        $client = new Client([
            'handler' => HandlerStack::create($mock),
            'base_uri' => 'https://rest.pay.nl/v2/',
        ]);
        $request = new Request('orders');
        $request->applyClient($client);
        $response = new Response();

        $request->execute($response);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame($rawBody, $response->getRawBody());
        $this->assertTrue($response->hasErrors());
        $this->assertStringContainsString('Payment rejected', $response->getErrors());
    }

    public function testExecutePreservesResponseFromClientException(): void
    {
        $rawBody = '{"message":"Payment rejected"}';
        $mock = new MockHandler([
            RequestException::create(
                new GuzzleRequest('GET', 'https://rest.pay.nl/v2/orders'),
                new GuzzleResponse(422, [], $rawBody)
            ),
        ]);
        $client = new Client([
            'handler' => HandlerStack::create($mock),
            'base_uri' => 'https://rest.pay.nl/v2/',
        ]);
        $request = new Request('orders');
        $request->applyClient($client);
        $response = new Response();

        $request->execute($response);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame($rawBody, $response->getRawBody());
        $this->assertStringContainsString('Payment rejected', $response->getErrors());
    }

    public function testExecuteReturnsServerErrorOnConnectionFailure(): void
    {
        $mock = new MockHandler([
            new ConnectException('Connection failed', new GuzzleRequest('GET', 'https://rest.pay.nl/v2/orders')),
        ]);
        $client = new Client([
            'handler' => HandlerStack::create($mock),
            'base_uri' => 'https://rest.pay.nl/v2/',
        ]);
        $request = new Request('orders');
        $request->applyClient($client);
        $response = new Response();

        $request->execute($response);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertTrue($response->hasErrors());
        $this->assertStringContainsString('Connection failed', $response->getErrors());
    }
}
