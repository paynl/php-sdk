<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as HttpResponse;
use PayNL\Sdk\Api\Api;
use PayNL\Sdk\Application\Application;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\ServiceNotCreatedException;
use PayNL\Sdk\Http\GuzzleClientFactoryInterface;
use PayNL\Sdk\Request\Request;
use PayNL\Sdk\Response\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class GuzzleClientFactoryTest extends TestCase
{
    public function testDefaultClient(): void
    {
        $client = $this->createApi()->getClient();

        $this->assertSame(Client::class, get_class($client));
        $this->assertSame(Config::TGU1 . '/v1/', (string)$client->getConfig('base_uri'));
        $this->assertNull($client->getConfig('timeout'));
        $this->assertNull($client->getConfig('connect_timeout'));
    }

    public function testCustomFactory(): void
    {
        $client = new Client([
            'base_uri' => 'https://example.com/v2/',
            'timeout' => 5.0,
            'connect_timeout' => 2.0,
        ]);
        $factory = $this->createMock(GuzzleClientFactoryInterface::class);
        $factory->expects($this->once())
            ->method('create')
            ->with(['base_uri' => 'https://example.com/v2/'])
            ->willReturn($client);

        $config = new Config([
            'api' => ['url' => 'https://example.com', 'version' => 2],
        ]);
        $config->setGuzzleClientFactory($factory);
        $api = $this->createApi($config->toArray());

        $this->assertSame($client, $api->getClient());
        $this->assertSame(5.0, $api->getClient()->getConfig('timeout'));
        $this->assertSame(2.0, $api->getClient()->getConfig('connect_timeout'));
    }

    public function testCustomClientIsUsedWithAnotherUrl(): void
    {
        $httpResponse = new HttpResponse(200, [], '{"id":"test-order"}');
        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([[
                'base_uri' => Config::TGU1 . '/v1/',
                'handler' => new MockHandler([$httpResponse]),
            ]])
            ->onlyMethods(['send'])
            ->getMock();
        $client->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(RequestInterface::class), ['base_uri' => 'https://example.com/custom/'])
            ->willReturn($httpResponse);

        $factory = $this->createMock(GuzzleClientFactoryInterface::class);
        $factory->expects($this->once())->method('create')->willReturn($client);
        $config = new Config();
        $config->setGuzzleClientFactory($factory);
        $api = $this->createApi($config->toArray());
        $request = new Request('orders', 'GET', [], ['url' => 'https://example.com/custom/']);
        $response = new Response();

        $api->doHandle($request, $response);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"id":"test-order"}', $response->getRawBody());
        $this->assertSame(Config::TGU1 . '/v1/', (string)$client->getConfig('base_uri'));
    }

    public function testInvalidFactoryIsRejected(): void
    {
        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('The guzzle_client_factory option must implement ' . GuzzleClientFactoryInterface::class);

        $this->createApi(['guzzle_client_factory' => new \stdClass()]);
    }

    private function createApi(array $options = []): Api
    {
        $config = array_merge([
            'authentication' => ['username' => 'test-user', 'password' => 'test-password'],
        ], $options);

        return Application::init($config)->getServiceManager()->get('Api');
    }
}
