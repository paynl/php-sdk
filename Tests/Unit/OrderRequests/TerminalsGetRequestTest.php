<?php

declare(strict_types=1);

namespace Tests\Unit;

use PayNL\Sdk\Model\Request\TerminalsGetRequest;
use PHPUnit\Framework\TestCase;

class TerminalsGetRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructorStoresTerminalCode(): void
    {
        $request = new TerminalsGetRequest('T-123456');

        $this->assertInstanceOf(TerminalsGetRequest::class, $request);

        $pathParams = $request->getPathParameters();
        $this->assertArrayHasKey('terminalCode', $pathParams);
        $this->assertSame('T-123456', $pathParams['terminalCode']);
    }

    /**
     * @return void
     */
    public function testPathParametersContainTerminalCode(): void
    {
        $request = new TerminalsGetRequest('ABC-999');

        $params = $request->getPathParameters();

        $this->assertIsArray($params);
        $this->assertArrayHasKey('terminalCode', $params);
        $this->assertSame('ABC-999', $params['terminalCode']);
    }

    /**
     * @return void
     */
    public function testBodyParametersAreEmpty(): void
    {
        $request = new TerminalsGetRequest('T-123456');

        $this->assertSame([], $request->getBodyParameters());
    }
}
