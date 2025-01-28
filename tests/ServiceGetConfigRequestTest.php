<?php

use PHPUnit\Framework\TestCase;
use PayNL\Sdk\Model\Request\ServiceGetConfigRequest;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Response\ServiceGetConfigResponse;

class ServiceGetConfigRequestTest extends TestCase
{
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

    public function testGetPathParametersWithoutServiceId(): void
    {
        $serviceGetConfigRequest = new ServiceGetConfigRequest();

        $pathParameters = $serviceGetConfigRequest->getPathParameters();

        $this->assertIsArray($pathParameters);
        $this->assertEmpty($pathParameters);
    }

    public function testGetBodyParameters(): void
    {
        $serviceGetConfigRequest = new ServiceGetConfigRequest('service123');

        $bodyParameters = $serviceGetConfigRequest->getBodyParameters();

        $this->assertIsArray($bodyParameters);
        $this->assertEmpty($bodyParameters);
    }

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