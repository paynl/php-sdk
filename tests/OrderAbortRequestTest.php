<?php

use PHPUnit\Framework\TestCase;
use PayNL\Sdk\Model\Request\OrderAbortRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Pay\PayOrder;

class OrderAbortRequestTest extends TestCase
{
    public function testConstructor(): void
    {
        $transactionId = '123456';
        $orderAbortRequest = new OrderAbortRequest($transactionId);

        $this->assertInstanceOf(OrderAbortRequest::class, $orderAbortRequest);
    }

    public function testGetPathParameters(): void
    {
        $transactionId = '123456';
        $orderAbortRequest = new OrderAbortRequest($transactionId);

        $pathParameters = $orderAbortRequest->getPathParameters();

        $this->assertIsArray($pathParameters);
        $this->assertArrayHasKey('transactionId', $pathParameters);
        $this->assertSame($transactionId, $pathParameters['transactionId']);
    }

    public function testGetBodyParameters(): void
    {
        $transactionId = '123456';
        $orderAbortRequest = new OrderAbortRequest($transactionId);

        $bodyParameters = $orderAbortRequest->getBodyParameters();

        $this->assertIsArray($bodyParameters);
        $this->assertEmpty($bodyParameters);
    }

    public function testStart(): void
    {
        $transactionId = '123456';
        $orderAbortRequest = $this->getMockBuilder(OrderAbortRequest::class)
            ->setConstructorArgs([$transactionId])
            ->onlyMethods(['start'])
            ->getMock();

        $mockPayOrder = $this->createMock(PayOrder::class);

        $orderAbortRequest->method('start')->willReturn($mockPayOrder);

        $result = $orderAbortRequest->start();

        $this->assertInstanceOf(PayOrder::class, $result);
    }

}