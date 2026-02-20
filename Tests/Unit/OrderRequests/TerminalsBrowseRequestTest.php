<?php

namespace Tests\Unit;

use PayNL\Sdk\Application\Application;
use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Request\TerminalsBrowseRequest;
use PHPUnit\Framework\TestCase;

class TerminalsBrowseRequestTest extends TestCase
{
    /**
     * @return void
     * @throws PayException
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testStartThrowsExceptionWithoutConfig()
    {
        $mockApplication = $this->createMock(Application::class);

        $mockApplication->expects($this->never())->method('request');

        $request = new TerminalsBrowseRequest();
        $request->setApplication($mockApplication);

        $this->expectException(PayException::class);
        $this->expectExceptionMessage('Please check your config');

        $request->start();
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testStartWrongConfig()
    {
        $mockApplication = $this->createMock(Application::class);

        $request = new TerminalsBrowseRequest();
        $request->setApplication($mockApplication);

        $config = (new Config())->setUsername('test')->setPassword('test');

        try {
            $request->setConfig($config)->start();
        } catch (PayException $e) {
            $this->assertEquals('Something went wrong', $e->getFriendlyMessage());
        }
    }

    /**
     * @return void
     */
    public function testPathParametersWithMerchantCode()
    {
        $request = new TerminalsBrowseRequest();
        $request->setMerchantCode('M-1111-2222');

        $params = $request->getPathParameters();

        $this->assertArrayHasKey('merchant[eq]', $params);
        $this->assertEquals('M-1111-2222', $params['merchant[eq]']);
        $this->assertArrayNotHasKey('merchant[neq]', $params);
    }

    /**
     * @return void
     */
    public function testPathParametersWithExcludeMerchantCode()
    {
        $request = new TerminalsBrowseRequest();
        $request->setExcludeMerchantCode('M-9999-8888');

        $params = $request->getPathParameters();

        $this->assertArrayHasKey('merchant[neq]', $params);
        $this->assertEquals('M-9999-8888', $params['merchant[neq]']);
        $this->assertArrayNotHasKey('merchant[eq]', $params);
    }

    /**
     * @return void
     */
    public function testPathParametersWithBoth()
    {
        $request = new TerminalsBrowseRequest();
        $request
            ->setMerchantCode('M-1111-2222')
            ->setExcludeMerchantCode('M-9999-8888');

        $params = $request->getPathParameters();

        $this->assertEquals('M-1111-2222', $params['merchant[eq]']);
        $this->assertEquals('M-9999-8888', $params['merchant[neq]']);
    }

    /**
     * @return void
     */
    public function testPathParametersEmpty()
    {
        $request = new TerminalsBrowseRequest();
        $params = $request->getPathParameters();
        $this->assertEmpty($params);
    }
}
