<?php

declare(strict_types=1);

namespace Tests\Unit;

use PayNL\Sdk\Model\Amount;
use PayNL\Sdk\Model\Pay\PayOrder;
use PHPUnit\Framework\TestCase;

class PayOrderTest extends TestCase
{
    public function testConstructorMapsPayloadObject(): void
    {
        $payload = [
            'object' => [
                'id'               => 'PO-123',
                'description'      => 'Test order',
                'reference'        => 'REF-999',
                'status'           => ['code' => 200, 'action' => 'PAID'],
                'amount'           => ['value' => 1000, 'currency' => 'EUR'],
                'capturedAmount'   => ['value' => 500, 'currency' => 'EUR'],
                'authorizedAmount' => ['value' => 1000, 'currency' => 'EUR'],
            ],
        ];

        $order = new PayOrder($payload);

        $this->assertSame('PO-123', $order->getId());
        $this->assertSame('Test order', $order->getDescription());
        $this->assertSame('REF-999', $order->getReference());
        $this->assertSame(200, $order->getStatusCode());
        $this->assertSame('PAID', $order->getStatusName());
    }

    public function testAmountRefundedIsNullWhenNotSet(): void
    {
        $order = new PayOrder();

        $this->assertNull($order->getAmountRefunded());
    }

    public function testAmountRefundedReturnsValueInUnits(): void
    {
        $order  = new PayOrder();
        $amount = new Amount(1234, 'EUR');

        $order->setAmountRefunded($amount);

        $this->assertSame(12.34, $order->getAmountRefunded());
    }

    public function testIsFastCheckoutTrueForPaymentBasedCheckout(): void
    {
        $order = new PayOrder();
        $order->setType('payment_based_checkout');

        $this->assertTrue($order->isFastCheckout());
    }

    public function testIsFastCheckoutFalseForOtherType(): void
    {
        $order = new PayOrder();
        $order->setType('something_else');

        $this->assertFalse($order->isFastCheckout());
    }

    public function testFastCheckoutDataDelegatesToCheckoutData(): void
    {
        $order = new PayOrder();
        $data  = ['foo' => 'bar', 'baz' => 123];

        $order->setCheckoutData($data);

        $this->assertSame($data, $order->getFastCheckoutData());
    }

    public function testStatusHelpersWithSetStatusCodeName(): void
    {
        $order = new PayOrder();

        $order->setStatusCodeName(321, 'PENDING');

        $this->assertSame(321, $order->getStatusCode());
        $this->assertSame('PENDING', $order->getStatusName());
    }

    public function testStatusHelpersWithSetStatus(): void
    {
        $order  = new PayOrder();
        $status = ['code' => 999, 'action' => 'SOMETHING'];

        $order->setStatus($status);

        $this->assertSame(999, $order->getStatusCode());
        $this->assertSame('SOMETHING', $order->getStatusName());
        $this->assertSame($status, $order->getStatus());
    }

    public function testPaymentHelpersReturnFirstPaymentData(): void
    {
        $order = new PayOrder();

        $payments = [
            [
                'paymentMethod' => ['id' => 'IDEAL'],
                'customerId'    => 'CUST-1',
                'customerName'  => 'John Doe',
            ],
        ];

        $order->setPayments($payments);

        $this->assertSame('IDEAL', $order->getPaymentMethod());
        $this->assertSame('CUST-1', $order->getCustomerId());
        $this->assertSame('John Doe', $order->getCustomerName());
    }

    public function testPaymentHelpersReturnNullWhenNoPayments(): void
    {
        $order = new PayOrder();

        $this->assertNull($order->getPaymentMethod());
        $this->assertNull($order->getCustomerId());
        $this->assertNull($order->getCustomerName());
        $this->assertSame([], $order->getPayments());
    }

    public function testIsTestmodeTrueWhenIntegrationTestFlagTrue(): void
    {
        $order = new PayOrder();

        $order->setIntegration(['test' => true]);

        $this->assertTrue($order->isTestmode());
    }

    public function testIsTestmodeFalseWhenIntegrationTestFlagMissingOrFalse(): void
    {
        $order = new PayOrder();

        $order->setIntegration(['foo' => 'bar']);
        $this->assertFalse($order->isTestmode());

        $order->setIntegration(['test' => false]);
        $this->assertFalse($order->isTestmode());
    }

    public function testCheckoutDataReturnsEmptyArrayWhenNotSet(): void
    {
        $order = new PayOrder();

        $this->assertSame([], $order->getCheckoutData());
    }

    public function testAmountAndCurrency(): void
    {
        $order  = new PayOrder();
        $amount = new Amount(2500, 'EUR'); // 25.00

        $order->setAmount($amount);

        $this->assertEquals(25.00, $order->getAmount());
        $this->assertSame('EUR', $order->getCurrency());
    }

    public function testAuthorizedAndCapturedAmountGettersAndSetters(): void
    {
        $order      = new PayOrder();
        $authorized = new Amount(1000, 'EUR');
        $captured   = new Amount(500, 'EUR');

        $this->assertNull($order->getAuthorizedAmount());
        $this->assertNull($order->getCapturedAmount());

        $order->setAuthorizedAmount($authorized);
        $order->setCapturedAmount($captured);

        $this->assertSame($authorized, $order->getAuthorizedAmount());
        $this->assertSame($captured, $order->getCapturedAmount());
    }

    public function testPaymentAndStatusUrls(): void
    {
        $order = new PayOrder();

        $links = [
            'redirect' => 'https://pay.nl/redirect/123',
            'status'   => 'https://pay.nl/status/123',
        ];

        $order->setLinks($links);

        $this->assertSame($links, $order->getLinks());
        $this->assertSame('https://pay.nl/redirect/123', $order->getPaymentUrl());
        $this->assertSame('https://pay.nl/status/123', $order->getStatusUrl());
    }

    public function testChargebackDetection(): void
    {
        $order = new PayOrder();

        $order->setStatus(['code' => 123, 'action' => 'CHARGEBACK']);
        $this->assertTrue($order->isChargeBack());

        $order->setStatus(['code' => 123, 'action' => 'PAID']);
        $this->assertFalse($order->isChargeBack());
    }

    public function testTransferDataGetterAndSetter(): void
    {
        $order = new PayOrder();

        $data = ['some' => 'value', 'foo' => 'bar'];

        $order->setTransferData($data);
        $this->assertSame($data, $order->getTransferData());

        $order->setTransferData(null);
        $this->assertNull($order->getTransferData());
    }

    public function testStatsGettersReturnNullWhenNotSet(): void
    {
        $order = new PayOrder();

        // Typed property $stats is not initialized unless setStats() is called
        $order->setStats([]);

        $this->assertSame([], $order->getStats());
        $this->assertNull($order->getExtra1());
        $this->assertNull($order->getExtra2());
        $this->assertNull($order->getExtra3());
        $this->assertNull($order->getTool());
        $this->assertNull($order->getInfo());
        $this->assertNull($order->getObject());
        $this->assertNull($order->getDomainId());
    }


    public function testStatsGettersReturnValuesWhenSet(): void
    {
        $order = new PayOrder();

        $stats = [
            'extra1'   => 'E1',
            'extra2'   => 'E2',
            'extra3'   => 'E3',
            'tool'     => 'api',
            'info'     => ['k' => 'v'],
            'object'   => 'order',
            'domainId' => 123,
        ];

        $order->setStats($stats);

        $this->assertSame($stats, $order->getStats());
        $this->assertSame('E1', $order->getExtra1());
        $this->assertSame('E2', $order->getExtra2());
        $this->assertSame('E3', $order->getExtra3());
        $this->assertSame('api', $order->getTool());
        $this->assertSame(['k' => 'v'], $order->getInfo());
        $this->assertSame('order', $order->getObject());
        $this->assertSame(123, $order->getDomainId());
    }
}
