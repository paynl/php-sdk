<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class VatFunctionsTest extends TestCase
{
    /**
     * @return void
     */
    public function testCalcVatPercentage(): void
    {
        $percentage = paynl_calc_vat_percentage(121.00, 21.00);

        $this->assertIsFloat($percentage);
        $this->assertEqualsWithDelta(21.0, $percentage, 0.001);
    }

    /**
     * @return void
     */
    public function testDetermineVatClassIsConsistentWithPercentageHelper(): void
    {
        $amountIncludingVat = 121.00;
        $vatAmount          = 21.00;

        $vatClassFromAmounts = paynl_determine_vat_class($amountIncludingVat, $vatAmount);

        $percentage          = paynl_calc_vat_percentage($amountIncludingVat, $vatAmount);
        $vatClassFromPercent = paynl_determine_vat_class_by_percentage($percentage);

        $this->assertIsString($vatClassFromAmounts);
        $this->assertIsString($vatClassFromPercent);
        $this->assertSame($vatClassFromAmounts, $vatClassFromPercent);
    }
}
