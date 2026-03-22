<?php

declare(strict_types=1);

use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Model\Request\OrderVoidRequest;
use PayNL\Sdk\Request\RequestData;
use PHPUnit\Framework\TestCase;

final class OrderVoidRequestTest extends TestCase
{
    public function testExtendsRequestData(): void
    {
        $reflection = new \ReflectionClass(OrderVoidRequest::class);
        /** @var OrderVoidRequest $request */
        $request = $reflection->newInstanceWithoutConstructor();

        $this->assertInstanceOf(RequestData::class, $request);
    }

    public function testGetBodyParametersReturnsEmptyArray(): void
    {
        $reflection = new \ReflectionClass(OrderVoidRequest::class);
        /** @var OrderVoidRequest $request */
        $request = $reflection->newInstanceWithoutConstructor();

        $this->assertSame([], $request->getBodyParameters());
    }

    public function testStartMethodHasPayOrderReturnType(): void
    {
        $method = new \ReflectionMethod(OrderVoidRequest::class, 'start');
        $returnType = $method->getReturnType();

        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame(PayOrder::class, $returnType->getName());
        $this->assertFalse($returnType->allowsNull());
    }
}
