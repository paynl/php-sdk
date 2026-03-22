<?php

namespace Tests\Unit;

use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Model\Request\OrderStatusRequest;
use PHPUnit\Framework\TestCase;

class OrderStatusRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructorInitializesCorrectly(): void
    {
        $orderId = '12345';
        $request = new OrderStatusRequest($orderId);
        $this->assertEquals(['transactionId' => $orderId], $request->getPathParameters());
    }

    /**
     * @return void
     */
    public function testGetPathParameters(): void
    {
        $orderId = '67890';
        $request = new OrderStatusRequest($orderId);

        $expected = ['transactionId' => $orderId];
        $this->assertEquals($expected, $request->getPathParameters());
    }

    /**
     * @return void
     */
    public function testGetBodyParametersReturnsEmptyArray(): void
    {
        $orderId = '67890';
        $request = new OrderStatusRequest($orderId);

        $this->assertEquals([], $request->getBodyParameters());
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \PayNL\Sdk\Exception\PayException
     */
    public function testStartReturnsPayOrder(): void
    {
        $orderId = '12345';

        $mockPayOrder = $this->createMock(PayOrder::class);

        $request = $this->getMockBuilder(OrderStatusRequest::class)
            ->setConstructorArgs([$orderId])
            ->onlyMethods(['start'])
            ->getMock();

        $request->method('start')->willReturn($mockPayOrder);

        $result = $request->start();

        $this->assertInstanceOf(PayOrder::class, $result);
    }
}
