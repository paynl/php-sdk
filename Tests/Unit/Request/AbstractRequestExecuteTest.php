<?php

declare(strict_types=1);

namespace Tests\Unit\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PayNL\Sdk\Request\Request;
use PayNL\Sdk\Response\Response;
use PHPUnit\Framework\TestCase;

class AbstractRequestExecuteTest extends TestCase
{
    public function testExecuteSendsGetAgainstClientBaseUri(): void
    {
        $mock = new MockHandler([
            new GuzzleResponse(200, ['Content-Type' => 'application/json'], '{"id":"1"}'),
        ]);
        $client = new Client([
            'handler' => HandlerStack::create($mock),
            'base_uri' => 'https://rest.pay.nl/v2/',
        ]);

        $request = new Request('orders', Request::METHOD_GET);
        $request->applyClient($client, 'https://rest.pay.nl/v2/');

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
        $request->applyClient($client, 'https://rest.pay.nl/v2/');

        $response = new Response();
        $request->execute($response);

        $this->assertSame(200, $response->getStatusCode());

        $lastRequest = $mock->getLastRequest();
        $this->assertNotNull($lastRequest);
        $this->assertSame('POST', $lastRequest->getMethod());
        $this->assertSame('https://failover.pay.nl/orders', (string)$lastRequest->getUri());
    }
}
