<?php

use PHPUnit\Framework\TestCase;
use PayNL\Sdk\Model\Request\OrderApproveRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Pay\PayOrder;

class OrderApproveRequestTest extends TestCase
{
    public function testConstructor(): void
    {
        $transactionId = '123456';
        $orderApproveRequest = new OrderApproveRequest($transactionId);

        $this->assertInstanceOf(OrderApproveRequest::class, $orderApproveRequest);
    }

    public function testGetPathParameters(): void
    {
        $transactionId = '123456';
        $orderApproveRequest = new OrderApproveRequest($transactionId);

        $pathParameters = $orderApproveRequest->getPathParameters();

        $this->assertIsArray($pathParameters);
        $this->assertArrayHasKey('transactionId', $pathParameters);
        $this->assertSame($transactionId, $pathParameters['transactionId']);
    }

    public function testGetBodyParameters(): void
    {
        $transactionId = '123456';
        $orderApproveRequest = new OrderApproveRequest($transactionId);

        $bodyParameters = $orderApproveRequest->getBodyParameters();

        $this->assertIsArray($bodyParameters);
        $this->assertEmpty($bodyParameters);
    }

    public function testStart(): void
    {
        $transactionId = '123456';
        $orderApproveRequest = $this->getMockBuilder(OrderApproveRequest::class)
            ->setConstructorArgs([$transactionId])
            ->onlyMethods(['start'])
            ->getMock();

        $mockPayOrder = $this->createMock(PayOrder::class);

        $orderApproveRequest->method('start')->willReturn($mockPayOrder);

        $result = $orderApproveRequest->start();

        $this->assertInstanceOf(PayOrder::class, $result);
    }

}