<?php

declare(strict_types=1);

namespace Tests\Unit;

use PayNL\Sdk\Util\Exchange;
use PayNL\Sdk\Util\ExchangeResponse;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ExchangeTest extends TestCase
{
    private function createExchange(): Exchange
    {
        return new Exchange();
    }

    /**
     * Kleine helper om private $headers in Exchange te zetten,
     * zodat getRequestHeaders() geen getallheaders() hoeft te doen.
     *
     * @param Exchange $exchange
     * @param array    $headers
     * @return void
     */
    private function setHeaders(Exchange $exchange, array $headers): void
    {
        $refClass = new ReflectionClass(Exchange::class);
        $prop     = $refClass->getProperty('headers');
        $prop->setAccessible(true);
        $prop->setValue($exchange, $headers);
    }

    public function testDefaultGmsReferenceKeyIsExtra1(): void
    {
        $exchange = $this->createExchange();

        $this->assertSame('extra1', $exchange->getGmsReferenceKey());
    }

    public function testSetGmsReferenceKeyOverridesDefault(): void
    {
        $exchange = $this->createExchange();

        $exchange->setGmsReferenceKey('custom_ref');

        $this->assertSame('custom_ref', $exchange->getGmsReferenceKey());
    }

    public function testSetResponseNonSignedExchangeFormatsPlainText(): void
    {
        $exchange = $this->createExchange();

        $this->setHeaders($exchange, ['signature-method' => 'PLAINTEXT']);

        $output = $exchange->setResponse(false, 'SoMe MSG', true);

        $this->assertSame('FALSE| Some msg', $output);
    }

    public function testSetResponseSignedExchangeFormatsJson(): void
    {
        $exchange = $this->createExchange();

        $this->setHeaders($exchange, ['signature-method' => 'HMAC']);

        $output = $exchange->setResponse(true, 'oK', true);

        $decoded = json_decode((string)$output, true);

        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('result', $decoded);
        $this->assertArrayHasKey('description', $decoded);

        $this->assertTrue($decoded['result']);
        $this->assertSame('Ok', $decoded['description']); // ucfirst(strtolower('oK'))
    }

    public function testSetExchangeResponseDelegatesToSetResponse(): void
    {
        $exchange = $this->createExchange();

        $this->setHeaders($exchange, ['signature-method' => 'PLAINTEXT']);

        $eResponse = new ExchangeResponse(false, 'error occurred');

        $output = $exchange->setExchangeResponse($eResponse, true);

        $this->assertSame('FALSE| Error occurred', $output);
    }

    public function testIsSignExchangeTrueWhenSignatureMethodIsHmac(): void
    {
        $exchange = $this->createExchange();

        $this->setHeaders($exchange, ['signature-method' => 'HMAC']);

        $this->assertTrue($exchange->isSignExchange());
    }

    public function testIsSignExchangeFalseWhenSignatureMethodMissingOrNotHmac(): void
    {
        $exchange = $this->createExchange();

        $this->setHeaders($exchange, []);
        $this->assertFalse($exchange->isSignExchange());

        $this->setHeaders($exchange, ['signature-method' => 'SOMETHING_ELSE']);
        $this->assertFalse($exchange->isSignExchange());
    }

    public function testCheckSignExchangeReturnsFalseWhenNotSigningExchange(): void
    {
        $exchange = $this->createExchange();

        $this->setHeaders($exchange, ['signature-method' => 'PLAINTEXT']);

        $this->assertFalse($exchange->checkSignExchange('user', 'pass'));
    }

    public function testCheckSignExchangeReturnsFalseWhenSignatureValidationFails(): void
    {
        $exchange = $this->createExchange();

        $this->setHeaders($exchange, [
            'signature-method'   => 'HMAC',
            'signature-keyid'    => 'user123',
            'signature-algorithm'=> 'sha256',
            'signature'          => 'invalid-signature',
        ]);

        $this->assertFalse($exchange->checkSignExchange('user123', 'secret'));
    }
}
