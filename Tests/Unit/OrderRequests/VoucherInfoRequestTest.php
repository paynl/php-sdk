<?php

namespace Tests\Unit;

use PayNL\Sdk\Application\Application;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\VoucherInfoRequest;
use PHPUnit\Framework\TestCase;

class VoucherInfoRequestTest extends TestCase
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

        $request = new VoucherInfoRequest();
        $request->setApplication($mockApplication);

        $this->expectException(PayException::class);
        $this->expectExceptionMessage('Please check your config');

        $request->start();
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testStartWrongConfig()
    {
        $mockApplication = $this->createMock(Application::class);

        $request = new VoucherInfoRequest();
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
    public function testSetServiceId(): void
    {
        $request = new VoucherInfoRequest();

        $request->setServiceId('SL-1234-5678');
        $reflection = new \ReflectionClass($request);
        $serviceIdProperty = $reflection->getProperty('serviceId');
        $serviceIdProperty->setAccessible(true);

        $this->assertEquals('SL-1234-5678', $serviceIdProperty->getValue($request));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetCardNumber(): void
    {
        $request = new VoucherInfoRequest();

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
        $request = new VoucherInfoRequest();

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
        $request = new VoucherInfoRequest();

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

        $request = new VoucherInfoRequest();
        $request->setPointOfInteraction('INVALID_POI');
    }

}
