<?php

namespace Tests\Unit;

use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Model\Request\OrderApproveRequest;
use PHPUnit\Framework\TestCase;

class OrderApproveRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $transactionId = '123456';
        $orderApproveRequest = new OrderApproveRequest($transactionId);

        $this->assertInstanceOf(OrderApproveRequest::class, $orderApproveRequest);
    }

    /**
     * @return void
     */
    public function testGetPathParameters(): void
    {
        $transactionId = '123456';
        $orderApproveRequest = new OrderApproveRequest($transactionId);

        $pathParameters = $orderApproveRequest->getPathParameters();

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
        $orderApproveRequest = new OrderApproveRequest($transactionId);

        $bodyParameters = $orderApproveRequest->getBodyParameters();

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
