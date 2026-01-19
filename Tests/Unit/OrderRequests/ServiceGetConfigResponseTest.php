<?php

declare(strict_types=1);

use PayNL\Sdk\Model\CheckoutOptions;
use PayNL\Sdk\Model\Method;
use PayNL\Sdk\Model\Response\ServiceGetConfigResponse;
use PHPUnit\Framework\TestCase;

final class ServiceGetConfigResponseTest extends TestCase
{
    public function testGetTerminalsReturnsOptionsForPinMethod(): void
    {
        $terminalOptions = [
            ['id' => 'T1', 'name' => 'Terminal 1'],
            ['id' => 'T2', 'name' => 'Terminal 2'],
        ];

        $pinMethod = $this->createMock(Method::class);
        $pinMethod->method('getId')->willReturn(Method::PIN);
        $pinMethod->method('hasOptions')->willReturn(true);
        $pinMethod->method('getOptions')->willReturn($terminalOptions);

        $otherMethod = $this->createMock(Method::class);
        $otherMethod->method('getId')->willReturn(10);
        $otherMethod->method('hasOptions')->willReturn(true);
        $otherMethod->method('getOptions')->willReturn([]);

        // simpele stub voor één checkoutOption
        $checkoutOption = new class ([$otherMethod, $pinMethod]) {
            /** @var Method[] */
            private array $methods;

            /**
             * @param Method[] $methods
             */
            public function __construct(array $methods)
            {
                $this->methods = $methods;
            }

            /**
             * @return Method[]
             */
            public function getPaymentMethods(): array
            {
                return $this->methods;
            }
        };

        $checkoutOptions = $this
            ->getMockBuilder(CheckoutOptions::class)
            ->onlyMethods(['getIterator'])
            ->getMock();

        $checkoutOptions
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$checkoutOption]));

        $response = new ServiceGetConfigResponse();
        $response->setCheckoutOptions($checkoutOptions);

        $this->assertSame($terminalOptions, $response->getTerminals());
    }

    public function testGetTerminalsReturnsEmptyArrayWhenNoPinWithOptions(): void
    {
        $otherMethod = $this->createMock(Method::class);
        $otherMethod->method('getId')->willReturn(10);
        $otherMethod->method('hasOptions')->willReturn(true);
        $otherMethod->method('getOptions')->willReturn([]);

        $checkoutOption = new class ([$otherMethod]) {
            private array $methods;

            public function __construct(array $methods)
            {
                $this->methods = $methods;
            }

            public function getPaymentMethods(): array
            {
                return $this->methods;
            }
        };

        $checkoutOptions = $this
            ->getMockBuilder(CheckoutOptions::class)
            ->onlyMethods(['getIterator'])
            ->getMock();

        $checkoutOptions
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$checkoutOption]));

        $response = new ServiceGetConfigResponse();
        $response->setCheckoutOptions($checkoutOptions);

        $this->assertSame([], $response->getTerminals());
    }
}
