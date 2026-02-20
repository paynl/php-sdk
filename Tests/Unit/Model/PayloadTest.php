<?php

declare(strict_types=1);

namespace Tests\Unit;

use PayNL\Sdk\Model\Pay\PayLoad;
use PHPUnit\Framework\TestCase;

class PayLoadTest extends TestCase
{
    public function testLegacyPayloadMapping(): void
    {
        $payload = [
            'type'              => 'SALE',
            'amount'            => 1000,
            'currency'          => 'EUR',
            'amount_cap'        => 1100.0,
            'amount_auth'       => 900.0,
            'reference'         => 'REF-123',
            'action'            => 'PAY',
            'payment_profile'   => 321,
            'pay_order_id'      => 'A123',
            'order_id'          => 'ORD-1',
            'internal_state_id' => 7,
            'internal_state_name' => 'STATE',
            'checkout_data'     => ['foo' => 'bar'],
            'full_payload'      => [
                'action' => 'legacy',
                'extra1' => 'LEG1',
                'extra2' => 'LEG2',
                'extra3' => 'LEG3',
                'stats'  => [
                    'extra1' => 'STAT1',
                    'extra2' => 'STAT2',
                    'extra3' => 'STAT3',
                ],
            ],
        ];

        $load = new PayLoad($payload);

        // basic mapping
        $this->assertSame('SALE', $load->getType());
        $this->assertSame(1000, $load->getAmount());
        $this->assertSame('EUR', $load->getCurrency());
        $this->assertSame(1100.0, $load->getAmountCap());
        $this->assertSame(900.0, $load->getAmountAuth());
        $this->assertSame('REF-123', $load->getReference());
        $this->assertSame('PAY', $load->getAction());
        $this->assertSame(321, $load->getPaymentProfile());
        $this->assertSame('A123', $load->getPayOrderId());
        $this->assertSame(7, $load->getInternalStateId());
        $this->assertSame('STATE', $load->getInternalStateName());
        $this->assertSame(['foo' => 'bar'], $load->getCheckoutData());

        $this->assertSame($payload['full_payload'], $load->getFullPayLoad());
        $this->assertSame('LEG1', $load->getExtra1());
        $this->assertSame('LEG2', $load->getExtra2());
        $this->assertSame('LEG3', $load->getExtra3());

        // legacy vs TGU
        $this->assertFalse($load->isTguLoad());
        $this->assertTrue($load->isLegacyPayLoad());
    }

    public function testTguLoadMappingAndFlags(): void
    {
        $payload = [
            'type'              => 'SALE',
            'amount'            => 2000,
            'currency'          => 'EUR',
            'amount_cap'        => 0.0,
            'amount_auth'       => 0.0,
            'reference'         => 'TGU-REF',
            'action'            => 'PAY',
            'payment_profile'   => 7,
            'pay_order_id'      => '5ABC', // voor isTguTransaction
            'order_id'          => 'ORD-2',
            'internal_state_id' => 1,
            'internal_state_name' => 'STATE-2',
            'checkout_data'     => ['baz' => 'qux'],
            'full_payload'      => [
                'object' => [
                    'extra1' => 'OBJ1',
                    'stats'  => [
                        'extra1' => 'STAT-OBJ1',
                    ],
                ],
            ],
        ];

        $load = new PayLoad($payload);

        $this->assertTrue($load->isTguLoad());
        $this->assertFalse($load->isLegacyPayLoad());

        $this->assertSame($payload['full_payload'], $load->getFullPayLoad());
        $this->assertSame('OBJ1', $load->getExtra1());
        $this->assertSame('', $load->getExtra2());
        $this->assertSame('', $load->getExtra3());

        $this->assertSame(['baz' => 'qux'], $load->getCheckoutData());

        $this->assertTrue($load->isTguTransaction());
    }

    public function testIsTguTransactionFalseCases(): void
    {
        $basePayload = [
            'type'              => 'SALE',
            'amount'            => 0,
            'currency'          => 'EUR',
            'amount_cap'        => 0.0,
            'amount_auth'       => 0.0,
            'reference'         => '',
            'action'            => '',
            'payment_profile'   => 0,
            'order_id'          => '',
            'internal_state_id' => 0,
            'internal_state_name' => '',
            'checkout_data'     => [],
            'full_payload'      => [
                'action' => 'legacy',
            ],
        ];

        $payload1               = $basePayload;
        $payload1['pay_order_id'] = '3ABC';
        $load1                  = new PayLoad($payload1);
        $this->assertFalse($load1->isTguTransaction());

        $payload2               = $basePayload;
        $payload2['pay_order_id'] = 'A123';
        $load2                  = new PayLoad($payload2);
        $this->assertFalse($load2->isTguTransaction());

        $payload3               = $basePayload;
        $payload3['pay_order_id'] = '';
        $load3                  = new PayLoad($payload3);
        $this->assertFalse($load3->isTguTransaction());
    }

    public function testIsFastCheckout(): void
    {
        $basePayload = [
            'amount'            => 0,
            'currency'          => 'EUR',
            'amount_cap'        => 0.0,
            'amount_auth'       => 0.0,
            'reference'         => '',
            'action'            => '',
            'payment_profile'   => 0,
            'pay_order_id'      => '',
            'order_id'          => '',
            'internal_state_id' => 0,
            'internal_state_name' => '',
            'checkout_data'     => [],
            'full_payload'      => ['action' => 'legacy'],
        ];

        $payload1        = $basePayload;
        $payload1['type'] = 'payment_based_checkout';
        $load1           = new PayLoad($payload1);
        $this->assertTrue($load1->isFastCheckout());

        $payload2        = $basePayload;
        $payload2['type'] = 'PAYMENT_BASED_CHECKOUT';
        $load2           = new PayLoad($payload2);
        $this->assertTrue($load2->isFastCheckout());

        $payload3        = $basePayload;
        $payload3['type'] = 'regular_payment';
        $load3           = new PayLoad($payload3);
        $this->assertFalse($load3->isFastCheckout());
    }
}
