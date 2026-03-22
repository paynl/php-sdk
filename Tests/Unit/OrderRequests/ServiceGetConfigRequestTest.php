<?php

namespace Tests\Unit;

use PayNL\Sdk\Model\Request\ServiceGetConfigRequest;
use PayNL\Sdk\Model\Response\ServiceGetConfigResponse;
use PHPUnit\Framework\TestCase;

class ServiceGetConfigRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructor(): void
    {
        $serviceId = 'service123';
        $serviceGetConfigRequest = new ServiceGetConfigRequest($serviceId);

        $this->assertInstanceOf(ServiceGetConfigRequest::class, $serviceGetConfigRequest);
    }

    /**
     * @return void
     */
    public function testGetPathParametersWithServiceId(): void
    {
        $serviceId = 'service123';
        $serviceGetConfigRequest = new ServiceGetConfigRequest($serviceId);

        $pathParameters = $serviceGetConfigRequest->getPathParameters();

        $this->assertIsArray($pathParameters);
        $this->assertArrayHasKey('serviceId', $pathParameters);
        $this->assertSame($serviceId, $pathParameters['serviceId']);
    }

    /**
     * @return void
     */
    public function testGetPathParametersWithoutServiceId(): void
    {
        $serviceGetConfigRequest = new ServiceGetConfigRequest();

        $pathParameters = $serviceGetConfigRequest->getPathParameters();

        $this->assertIsArray($pathParameters);
        $this->assertEmpty($pathParameters);
    }

    /**
     * @return void
     */
    public function testGetBodyParameters(): void
    {
        $serviceGetConfigRequest = new ServiceGetConfigRequest('service123');

        $bodyParameters = $serviceGetConfigRequest->getBodyParameters();

        $this->assertIsArray($bodyParameters);
        $this->assertEmpty($bodyParameters);
    }

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     * @throws \PayNL\Sdk\Exception\PayException
     */
    public function testStart(): void
    {
        $serviceId = 'service123';
        $serviceGetConfigRequest = $this->getMockBuilder(ServiceGetConfigRequest::class)
            ->setConstructorArgs([$serviceId])
            ->onlyMethods(['start'])
            ->getMock();

        $mockResponse = $this->createMock(ServiceGetConfigResponse::class);

        $serviceGetConfigRequest->method('start')->willReturn($mockResponse);

        $result = $serviceGetConfigRequest->start();

        $this->assertInstanceOf(ServiceGetConfigResponse::class, $result);
    }
}
