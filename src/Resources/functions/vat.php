<?php

declare(strict_types=1);

use PayNL\Sdk\Util\Vat;

if (false === function_exists('paynl_calc_vat_percentage')) {
    /**
     * @param float $amountIncludingVat
     * @param float $vatAmount
     * @return float
     */
    function paynl_calc_vat_percentage(float $amountIncludingVat, float $vatAmount): float
    {
        return (new Vat())->calculatePercentage($amountIncludingVat, $vatAmount);
    }
}

if (false === function_exists('paynl_determine_vat_class')) {
    /**
     * @param float $amountIncludingVat
     * @param float $vatAmount
     * @return string
     */
    function paynl_determine_vat_class(float $amountIncludingVat, float $vatAmount): string
    {
        $vat = new Vat();

        return $vat->determineVatClass(
          paynl_calc_vat_percentage($amountIncludingVat, $vatAmount)
        );
    }
}

if (false === function_exists('paynl_determine_vat_class_by_percentage')) {
    /**
     * @param float $percentage
     * @return string
     */
    function paynl_determine_vat_class_by_percentage(float $percentage): string
    {
        return (new Vat())->determineVatClass($percentage);
    }
}
