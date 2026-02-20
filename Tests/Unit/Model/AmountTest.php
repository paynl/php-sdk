<?php

namespace Tests\Unit\Model;

use PayNL\Sdk\Model\Amount;
use PHPUnit\Framework\TestCase;

class AmountTest extends TestCase
{
    /**
     * @return void
     * @throws \Exception
     */
    public function testConstructFromFloat(): void
    {
        $money = Amount::fromFloat(123.45);
        $this->assertEquals(12345, $money->getValue());
        $this->assertEquals('EUR', $money->getCurrency());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testConstructFromFloatWithCurrency(): void
    {
        $money = Amount::fromFloat(123, 'USD');
        $this->assertEquals(12300, $money->getValue());
        $this->assertEquals('USD', $money->getCurrency());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testConstructFromCents(): void
    {
        $money = Amount::fromCents(12345);
        $this->assertEquals(12345, $money->getValue());
        $this->assertEquals('EUR', $money->getCurrency());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testConstructFromCentsWithCurrency(): void
    {
        $money = Amount::fromCents(123, 'USD');
        $this->assertEquals(123, $money->getValue());
        $this->assertEquals('USD', $money->getCurrency());
    }
}
