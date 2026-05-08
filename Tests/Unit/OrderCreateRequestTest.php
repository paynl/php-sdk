<?php

declare(strict_types=1);

namespace Tests\Unit;

use PayNL\Sdk\Model\Customer;
use PayNL\Sdk\Model\Order;
use PayNL\Sdk\Model\Request\OrderCreateRequest;
use PayNL\Sdk\Model\Stats;
use PHPUnit\Framework\TestCase;

class OrderCreateRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function testEnableFastCheckout(): void
    {
        $request = new OrderCreateRequest();
        $request->enableFastCheckout(false, true, false);

        $reflection = new \ReflectionClass($request);
        $optimizeProperty = $reflection->getProperty('optimize');
        $optimizeProperty->setAccessible(true);

        $this->assertEquals([
            'flow' => 'fastCheckout',
            'shippingAddress' => false,
            'billingAddress' => true,
            'contactDetails' => false,
        ], $optimizeProperty->getValue($request));
    }

    /**
     * @return void
     */
    public function testSetReturnUrl(): void
    {
        $request = new OrderCreateRequest();
        $request->setReturnurl('https://example.com/return');

        $reflection = new \ReflectionClass($request);
        $returnUrlProperty = $reflection->getProperty('returnUrl');
        $returnUrlProperty->setAccessible(true);

        $this->assertEquals('https://example.com/return', $returnUrlProperty->getValue($request));
    }

    /**
     * @return void
     */
    public function testSetAmount(): void
    {
        $request = new OrderCreateRequest();
        $request->setAmount(123.45);

        $reflection = new \ReflectionClass($request);
        $amountProperty = $reflection->getProperty('amount');
        $amountProperty->setAccessible(true);

        $this->assertEquals(12345, $amountProperty->getValue($request));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetReference(): void
    {
        $request = new OrderCreateRequest();

        $request->setReference('Order123');

        $reflection = new \ReflectionClass($request);
        $referenceProperty = $reflection->getProperty('reference');
        $referenceProperty->setAccessible(true);

        $this->assertEquals('Order123', $referenceProperty->getValue($request));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetReferenceThrowsExceptionForInvalidReference(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Reference should consist of all letters or digits');

        $request = new OrderCreateRequest();
        $request->setReference('Order#123');
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetNotification(): void
    {
        $request = new OrderCreateRequest();

        $request->setNotification('email', 'test@example.com');

        $reflection = new \ReflectionClass($request);
        $notificationTypeProperty = $reflection->getProperty('notificationType');
        $notificationTypeProperty->setAccessible(true);
        $notificationRecipientProperty = $reflection->getProperty('notificationRecipient');
        $notificationRecipientProperty->setAccessible(true);

        $this->assertEquals('email', $notificationTypeProperty->getValue($request));
        $this->assertEquals('test@example.com', $notificationRecipientProperty->getValue($request));
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetNotificationThrowsExceptionForInvalidEmail(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Valid email format expected as notification recipient');

        $request = new OrderCreateRequest();
        $request->setNotification('email', 'invalid-email');
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetNotificationThrowsExceptionForInvalidPushRecipient(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Recepient expected to be AD-####-#### code');

        $request = new OrderCreateRequest();
        $request->setNotification('push', '1234');
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSetCustomer(): void
    {
        $mockCustomer = $this->createMock(Customer::class);
        $request = new OrderCreateRequest();
        $request->setCustomer($mockCustomer);

        $reflection = new \ReflectionClass($request);
        $customerProperty = $reflection->getProperty('customer');
        $customerProperty->setAccessible(true);

        $this->assertSame($mockCustomer, $customerProperty->getValue($request));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSetOrder(): void
    {
        $mockOrder = $this->createMock(Order::class);
        $request = new OrderCreateRequest();
        $request->setOrder($mockOrder);

        $reflection = new \ReflectionClass($request);
        $orderProperty = $reflection->getProperty('order');
        $orderProperty->setAccessible(true);

        $this->assertSame($mockOrder, $orderProperty->getValue($request));
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSetStats(): void
    {
        $mockStats = $this->createMock(Stats::class);
        $request = new OrderCreateRequest();
        $request->setStats($mockStats);

        $reflection = new \ReflectionClass($request);
        $statsProperty = $reflection->getProperty('stats');
        $statsProperty->setAccessible(true);

        $this->assertSame($mockStats, $statsProperty->getValue($request));
    }
}
