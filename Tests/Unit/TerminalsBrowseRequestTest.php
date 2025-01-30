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
}
