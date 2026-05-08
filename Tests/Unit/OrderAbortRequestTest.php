<?php

namespace Tests\Unit;

use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Model\Request\OrderAbortRequest;
use PHPUnit\Framework\TestCase;

class OrderAbortRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $transactionId = '123456';
        $orderAbortRequest = new OrderAbortRequest($transactionId);

        $this->assertInstanceOf(OrderAbortRequest::class, $orderAbortRequest);
    }

    /**
     * @return void
     */
    public function testGetPathParameters(): void
    {
        $transactionId = '123456';
        $orderAbortRequest = new OrderAbortRequest($transactionId);

        $pathParameters = $orderAbortRequest->getPathParameters();

        $this->assertIsArray($pathParameters);
        $this->assertArrayHasKey('transactionId', $pathParameters);
        $this->assertSame($transactionId, $pathParameters['transactionId']);
    }

    /**
     * @return void
     */
    public function testGetBodyParameters(): void
    {
        $transactionId = '123456';
        $orderAbortRequest = new OrderAbortRequest($transactionId);

        $bodyParameters = $orderAbortRequest->getBodyParameters();

        $this->assertIsArray($bodyParameters);
        $this->assertEmpty($bodyParameters);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \PayNL\Sdk\Exception\PayException
     */
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
