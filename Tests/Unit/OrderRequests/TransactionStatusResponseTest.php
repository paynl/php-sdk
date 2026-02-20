<?php

declare(strict_types=1);

use PayNL\Sdk\Model\Amount;
use PayNL\Sdk\Model\Response\TransactionStatusResponse;
use PHPUnit\Framework\TestCase;

final class TransactionStatusResponseTest extends TestCase
{
    public function testGetAmountRefundedConvertsMinorToMajorUnits(): void
    {
        $amount = $this->createMock(Amount::class);

        $amount->method('getValue')->willReturn(1234);
        $amount->method('getCurrency')->willReturn('EUR');

        $response = new TransactionStatusResponse();
        $response->setAmountRefunded($amount);

        $this->assertSame(12.34, $response->getAmountRefunded());
    }

    public function testGetAmountRefundedCurrencyUsesAmountCurrency(): void
    {
        $amount = $this->createMock(Amount::class);
        $amount->method('getValue')->willReturn(500);
        $amount->method('getCurrency')->willReturn('USD');

        $response = new TransactionStatusResponse();
        $response->setAmountRefunded($amount);

        $this->assertSame('USD', $response->getAmountRefundedCurrency());
    }
}
