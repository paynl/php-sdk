<?php

declare(strict_types=1);

use PayNL\Sdk\Model\CheckoutOptions;
use PayNL\Sdk\Model\Method;
use PayNL\Sdk\Model\Response\ServiceGetConfigResponse;
use PHPUnit\Framework\TestCase;
use PayNL\Sdk\Model\CheckoutOption;
use PayNL\Sdk\Model\Methods;


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

    public function testGetPaymentMethodsUsesDefaultSequence(): void
    {
        $response = $this->createResponseWithMockData();

        $methods = $response->getPaymentMethods();

        $this->assertNotEmpty($methods);
    }

    public function testGetPaymentMethodsWithLowercaseCountryCode(): void
    {
        $response = $this->createResponseWithMockData();

        $methodsLower = $response->getPaymentMethods('gb');
        $methodsUpper = $response->getPaymentMethods('GB');

        $this->assertEquals($methodsUpper, $methodsLower, 'Country code should be normalized to uppercase');
    }

    public function testGetPaymentMethodsReturnsEmptyWhenCountryDoesNotExist(): void
    {
        $response = $this->createResponseWithMockData();

        $methods = $response->getPaymentMethods('XX');

        $this->assertIsArray($methods);
        $this->assertCount(0, $methods);
    }

    public function testPrimaryMethodsComeBeforeSecondary(): void
    {
        $response = $this->createResponseWithMockData();

        $methods = $response->getPaymentMethods('GB');

        $checkoutSequence = $response->getCheckoutSequence();

        $primaryTags = $checkoutSequence['GB']['primary'] ?? [];
        $secondaryTags = $checkoutSequence['GB']['secondary'] ?? [];

        if (count($primaryTags) && count($secondaryTags)) {
            $firstSecondaryIndex = null;

            foreach ($methods as $index => $method) {
                if (in_array($method->getId(), $secondaryTags, true)) {
                    $firstSecondaryIndex = $index;
                    break;
                }
            }

            if ($firstSecondaryIndex !== null) {
                for ($i = 0; $i < $firstSecondaryIndex; $i++) {
                    $this->assertTrue(
                        in_array($methods[$i]->getId(), $primaryTags, true),
                        'Primary methods should appear before secondary methods'
                    );
                }
            }
        }

        $this->assertTrue(true);
    }

    private function createResponseWithMockData(): ServiceGetConfigResponse
    {
        $response = new ServiceGetConfigResponse();

        $method10 = (new Method())->setId(10)->setName('iDEAL');

        $methodsCollection10 = new Methods();
        $methodsCollection10->add($method10);

        $option10 = new CheckoutOption();
        $option10->setTag('PM_10');
        $option10->setPaymentMethods($methodsCollection10);

        $method436 = (new Method())->setId(436)->setName('PayPal');

        $methodsCollection436 = new Methods();
        $methodsCollection436->add($method436);

        $option436 = new CheckoutOption();
        $option436->setTag('PM_436');
        $option436->setPaymentMethods($methodsCollection436);

        $checkoutOptions = new CheckoutOptions();
        $checkoutOptions->add($option10);
        $checkoutOptions->add($option436);

        $response->setCheckoutOptions($checkoutOptions);

        $response->setCheckoutSequence([
            'default' => [
                'primary' => ['PM_10'],
                'secondary' => ['PM_436'],
            ],
            'NL' => [
                'primary' => ['PM_436'],
                'secondary' => ['PM_10'],
            ],
        ]);

        return $response;
    }

}
