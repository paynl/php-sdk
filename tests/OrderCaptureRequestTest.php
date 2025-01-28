<?php

use PHPUnit\Framework\TestCase;
use PayNL\Sdk\Model\Request\OrderCaptureRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Pay\PayOrder;

class OrderCaptureRequestTest extends TestCase
{
    public function testConstructor(): void
    {
        $transactionId = '123456';
        $amount = 100.50;
        $orderCaptureRequest = new OrderCaptureRequest($transactionId, $amount);

        $this->assertInstanceOf(OrderCaptureRequest::class, $orderCaptureRequest);
    }

    public function testGetPathParameters(): void
    {
        $transactionId = '123456';
        $orderCaptureRequest = new OrderCaptureRequest($transactionId);

        $pathParameters = $orderCaptureRequest->getPathParameters();

        $this->assertIsArray($pathParameters);
        $this->assertArrayHasKey('transactionId', $pathParameters);
        $this->assertSame($transactionId, $pathParameters['transactionId']);
    }

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