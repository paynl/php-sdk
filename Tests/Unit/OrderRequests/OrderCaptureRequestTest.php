<?php

namespace Tests\Unit;

use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Model\Request\OrderCaptureRequest;
use PHPUnit\Framework\TestCase;

class OrderCaptureRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $transactionId = '123456';
        $amount = 100.50;
        $orderCaptureRequest = new OrderCaptureRequest($transactionId, $amount);

        $this->assertInstanceOf(OrderCaptureRequest::class, $orderCaptureRequest);
    }

    /**
     * @return void
     */
    public function testGetPathParameters(): void
    {
        $transactionId = '123456';
        $orderCaptureRequest = new OrderCaptureRequest($transactionId);

        $pathParameters = $orderCaptureRequest->getPathParameters();

        $this->assertIsArray($pathParameters);
        $this->assertArrayHasKey('transactionId', $pathParameters);
        $this->assertSame($transactionId, $pathParameters['transactionId']);
    }

    /**
     * @return void
     */
    public function testGetBodyParametersWithAmount(): void
    {
        $transactionId = '123456';
        $amount = 150.75;
        $orderCaptureRequest = new OrderCaptureRequest($transactionId, $amount);

        $bodyParameters = $orderCaptureRequest->getBodyParameters();

        $this->assertIsArray($bodyParameters);
        $this->assertArrayHasKey('amount', $bodyParameters);
        $this->assertSame((int)round($amount * 100), $bodyParameters['amount']);
    }

    /**
     * @return void
     */
    public function testSetProduct(): void
    {
        $transactionId = '123456';
        $orderCaptureRequest = new OrderCaptureRequest($transactionId);

        $productId = 'prod-001';
        $quantity = 2;

        $orderCaptureRequest->setProduct($productId, $quantity);

        $bodyParameters = $orderCaptureRequest->getBodyParameters();

        $this->assertIsArray($bodyParameters);
        $this->assertArrayHasKey('products', $bodyParameters);
        $this->assertCount(1, $bodyParameters['products']);
        $this->assertSame($productId, $bodyParameters['products'][0]['id']);
        $this->assertSame($quantity, $bodyParameters['products'][0]['quantity']);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \PayNL\Sdk\Exception\PayException
     */
    public function testStartWithAmount(): void
    {
        $transactionId = '123456';
        $amount = 200.00;
        $orderCaptureRequest = $this->getMockBuilder(OrderCaptureRequest::class)
            ->setConstructorArgs([$transactionId, $amount])
            ->onlyMethods(['start'])
            ->getMock();

        $mockPayOrder = $this->createMock(PayOrder::class);

        $orderCaptureRequest->method('start')->willReturn($mockPayOrder);

        $result = $orderCaptureRequest->start();

        $this->assertInstanceOf(PayOrder::class, $result);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \PayNL\Sdk\Exception\PayException
     */
    public function testStartWithProduct(): void
    {
        $transactionId = '123456';
        $orderCaptureRequest = $this->getMockBuilder(OrderCaptureRequest::class)
            ->setConstructorArgs([$transactionId])
            ->onlyMethods(['start'])
            ->getMock();

        $productId = 'prod-002';
        $quantity = 3;
        $orderCaptureRequest->setProduct($productId, $quantity);

        $mockPayOrder = $this->createMock(PayOrder::class);

        $orderCaptureRequest->method('start')->willReturn($mockPayOrder);

        $result = $orderCaptureRequest->start();

        $this->assertInstanceOf(PayOrder::class, $result);
    }
}
