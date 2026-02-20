<?php

declare(strict_types=1);

use PayNL\Sdk\Model\Pay\PayStatus;
use PHPUnit\Framework\TestCase;

final class PayStatusTest extends TestCase
{
    public function testGetMapsKnownNegativeStatuses(): void
    {
        $payStatus = new PayStatus();

        $this->assertSame(PayStatus::CHARGEBACK, $payStatus->get(-70));
        $this->assertSame(PayStatus::CHARGEBACK, $payStatus->get(-71));
        $this->assertSame(PayStatus::REFUND, $payStatus->get(-72));
        $this->assertSame(PayStatus::REFUND, $payStatus->get(-81));
        $this->assertSame(PayStatus::PARTIAL_REFUND, $payStatus->get(-82));
        $this->assertSame(PayStatus::VOID, $payStatus->get(-61));
        $this->assertSame(PayStatus::DENIED, $payStatus->get(-63));
        $this->assertSame(PayStatus::DENIED, $payStatus->get(-64));
    }

    public function testGetMapsKnownPositiveStatuses(): void
    {
        $payStatus = new PayStatus();

        $this->assertSame(PayStatus::PENDING, $payStatus->get(20));
        $this->assertSame(PayStatus::PENDING, $payStatus->get(25));
        $this->assertSame(PayStatus::PENDING, $payStatus->get(50));
        $this->assertSame(PayStatus::PENDING, $payStatus->get(90));

        $this->assertSame(PayStatus::CONFIRMED, $payStatus->get(75));
        $this->assertSame(PayStatus::CONFIRMED, $payStatus->get(76));

        $this->assertSame(PayStatus::PARTIAL_PAYMENT, $payStatus->get(80));
        $this->assertSame(PayStatus::VERIFY, $payStatus->get(85));
        $this->assertSame(PayStatus::AUTHORIZE, $payStatus->get(95));
        $this->assertSame(PayStatus::PARTLY_CAPTURED, $payStatus->get(97));
        $this->assertSame(PayStatus::PENDING, $payStatus->get(98));
        $this->assertSame(PayStatus::PAID, $payStatus->get(100));
    }

    public function testGetReturnsCancelForUnknownNegativeStatus(): void
    {
        $payStatus = new PayStatus();

        $this->assertSame(PayStatus::CANCEL, $payStatus->get(-5));
    }

    public function testGetThrowsExceptionForUnknownNonNegativeStatus(): void
    {
        $payStatus = new PayStatus();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unexpected status: 42');

        $payStatus->get(42);
    }
}
