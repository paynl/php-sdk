<?php

namespace Tests\Unit;

use PayNL\Sdk\Application\Application;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\VoucherPaymentRequest;
use PayNL\Sdk\Model\Pay\PayOrder;
use PHPUnit\Framework\TestCase;

class VoucherPaymentRequestTest extends TestCase
{
    /**
     * @return void
     * @throws PayException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testStartThrowsExceptionWithoutConfig()
    {
        $mockApplication = $this->createMock(Application::class);

        $mockApplication->expects($this->never())->method('request');

        $request = new VoucherPaymentRequest();
        $request->setApplication($mockApplication);

        $this->expectException(PayException::class);
        $this->expectExceptionMessage('Please check your config');

        $request->start();
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testStartWrongConfig(): void
    {
        $mockApplication = $this->createMock(Application::class);

        $request = new VoucherPaymentRequest();
        $request->setApplication($mockApplication);

        $config = (new Config())->setUsername('test')->setPassword('test');

        try {
            $request->setConfig($config)->start();
        } catch (PayException $e) {
            $this->assertEquals('Something went wrong', $e->getFriendlyMessage());
        }
    }
  
    /**
     * @return void
     * @throws \Exception
     */
    public function testSetCardNumber(): void
    {
        $request = new VoucherPaymentRequest();

        $request->setCardNumber('1234-5678-9012-3456');
        $reflection = new \ReflectionClass($request);
        $cardNumberProperty = $reflection->getProperty('cardNumber');
        $cardNumberProperty->setAccessible(true);

        $this->assertEquals('1234-5678-9012-3456', $cardNumberProperty->getValue($request));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetPinCode(): void
    {
        $request = new VoucherPaymentRequest();

        $request->setPinCode('123456');
        $reflection = new \ReflectionClass($request);
        $pinCodeProperty = $reflection->getProperty('pinCode');
        $pinCodeProperty->setAccessible(true);

        $this->assertEquals('123456', $pinCodeProperty->getValue($request));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetPointOfInteraction(): void
    {
        $request = new VoucherPaymentRequest();

        $request->setPointOfInteraction('ON_THE_MOVE');
        $reflection = new \ReflectionClass($request);
        $poiProperty = $reflection->getProperty('pointOfInteraction');
        $poiProperty->setAccessible(true);

        $this->assertEquals('ON_THE_MOVE', $poiProperty->getValue($request));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetPointOfInteractionThrowsExceptionForInvalidReference(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('pointOfInteraction should be one of: ON_THE_MOVE, ECOMMERCE, IN_PERSON, INVOICE, DEBT_COLLECTION, FUNDING, PAYMENT_REQUEST, RECURRING, UNATTENDED, MOTO, PAYOUT');

        $request = new VoucherPaymentRequest();
        $request->setPointOfInteraction('INVALID_POI');
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \PayNL\Sdk\Exception\PayException
     */
    public function testStart(): void
    {
        $serviceGetConfigRequest = $this->getMockBuilder(VoucherPaymentRequest::class)->getMock();
        $mockResponse = $this->createMock(PayOrder::class);
        $serviceGetConfigRequest->method('start')->willReturn($mockResponse);
        $result = $serviceGetConfigRequest->start();
        $this->assertInstanceOf(PayOrder::class, $result);
    }     
}
