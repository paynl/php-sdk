<?php

declare(strict_types=1);

namespace Tests\Unit;

use PayNL\Sdk\Model\Amount;
use PayNL\Sdk\Model\Response\VoucherInfoResponse;
use PHPUnit\Framework\TestCase;

class VoucherInfoResponseTest extends TestCase
{
    public function testsPayload(): void
    {
        $payload = [
            "integration" => [
                "test" => true,
                "pointOfInteraction" => "IN_PERSON"
            ],
            "voucher" => [
                "amount" => [
                    "value" => 1000,
                    "currency" => "EUR"
                ],
                "expiresAt" => null,
                "brand" => [
                    "id" => 1234,
                    "name" => "Cadeaukaart",
                    "iconUrl" => ""
                ]
            ]
        ];

        $response = new VoucherInfoResponse();

        $response->setIntegration($payload['integration']);
        $response->setVoucher($payload['voucher']);

        $this->assertTrue($response->getTest());
        $this->assertEquals(10.00, $response->getAmount());
        $this->assertSame('EUR', $response->getCurrency());
        $this->assertSame('IN_PERSON', $response->getPointOfInteraction());
        $this->assertSame(1234, $response->getBrandId());
        $this->assertSame('Cadeaukaart', $response->getBrandName());
    }

    public function testsPayloadWhenNotSet(): void
    {        
        $response = new VoucherInfoResponse();
        
        $this->assertFalse($response->getTest());
        $this->assertEquals(0, $response->getAmount());
        $this->assertSame('', $response->getCurrency());
        $this->assertSame('', $response->getPointOfInteraction());
        $this->assertSame(0, $response->getBrandId());
        $this->assertSame('', $response->getBrandName());
    }    
}
