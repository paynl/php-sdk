<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Response\ServiceGetConfigResponse;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class ServiceGetConfigRequest
 * Get the complete configuration of a service location. You can use this to create your own checkout.
 * Instead of using a tokencode/API-Token login, this function is also available when authenticated width slcode and secret.
 *
 * @package PayNL\Sdk\Model\Request
 */
class ServiceGetConfigRequest extends RequestData
{
    private string $serviceId;

    /**
     * @param $serviceId
     */
    public function __construct($serviceId = '')
    {
        $this->serviceId = $serviceId;
        parent::__construct('GetConfig', '/services/config', RequestInterface::METHOD_GET);
    }

    /**
     * @return array
     */
    public function getPathParameters(): array
    {
        if (!empty($this->serviceId)) {
            return ['serviceId' => $this->serviceId];
        }
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getBodyParameters(): array
    {
        return [];
    }

    /**
     * @return ServiceGetConfigResponse
     * @throws PayException
     */
    public function start(): ServiceGetConfigResponse
    {
        $this->config->setCore('https://rest.pay.nl');
        $this->config->setVersion(2);
        return parent::start();
    }
}