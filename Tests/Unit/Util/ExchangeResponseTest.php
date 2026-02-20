<?php

declare(strict_types=1);

use PayNL\Sdk\Util\ExchangeResponse;
use PHPUnit\Framework\TestCase;

final class ExchangeResponseTest extends TestCase
{
    private function getProperty(object $object, string $name): mixed
    {
        $ref = new \ReflectionClass($object);
        $prop = $ref->getProperty($name);
        $prop->setAccessible(true);

        return $prop->getValue($object);
    }

    public function testConstructorSetsResultAndMessage(): void
    {
        $response = new ExchangeResponse(true, 'OK');

        $this->assertTrue($this->getProperty($response, 'result'));
        $this->assertSame('OK', $this->getProperty($response, 'message'));
    }

    public function testSetUpdatesResultAndMessageAndIsFluent(): void
    {
        $response = new ExchangeResponse(false, 'Initial');

        $returned = $response->set(true, 'Updated');

        $this->assertSame($response, $returned);
        $this->assertTrue($this->getProperty($response, 'result'));
        $this->assertSame('Updated', $this->getProperty($response, 'message'));
    }
}
