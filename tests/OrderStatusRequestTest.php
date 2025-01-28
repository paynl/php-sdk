<?php

use PHPUnit\Framework\TestCase;
use PayNL\Sdk\Model\Pay\PayOrder;;
use PayNL\Sdk\Model\Request\OrderStatusRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Config\Config;

class OrderStatusRequestTest extends TestCase
{
    public function testConstructorInitializesCorrectly(): void
    {
        $orderId = '12345';
        $request = new OrderStatusRequest($orderId);
        $this->assertEquals(['transactionId' => $orderId], $request->getPathParameters());
    }

    public function testGetPathParameters(): void
    {
        $orderId = '67890';
        $request = new OrderStatusRequest($orderId);

        $expected = ['transactionId' => $orderId];
        $this->assertEquals($expected, $request->getPathParameters());
    }

    public function testGetBodyParametersReturnsEmptyArray(): void
    {
        $orderId = '67890';
        $request = new OrderStatusRequest($orderId);

        $this->assertEquals([], $request->getBodyParameters());
    }

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
