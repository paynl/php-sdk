<?php

declare(strict_types=1);

use PayNL\Sdk\Model\Amount;
use PayNL\Sdk\Model\Response\TransactionRefundResponse;
use PHPUnit\Framework\TestCase;

final class TransactionRefundResponseTest extends TestCase
{
    public function testAmountRefundedGetterAndSetter(): void
    {
        $amount = $this->createMock(Amount::class);

        $response = new TransactionRefundResponse();
        $response->setAmountRefunded($amount);

        $this->assertSame($amount, $response->getAmountRefunded());
    }

    public function testCreatedByGetterAndSetter(): void
    {
        $response  = new TransactionRefundResponse();
        $createdBy = 'A-5390-2487';

        $response->setCreatedBy($createdBy);

        $this->assertSame($createdBy, $response->getCreatedBy());
    }
}
