<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class OrderCaptureLegacyRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class OrderCaptureLegacyRequest extends RequestData
{
    private string $transactionId;

    /**
     * @param string $transactionId
     */
    public function __construct(string $transactionId)
    {
        $this->transactionId = $transactionId;
        parent::__construct('OrderCaptureLegacy', '/transaction/capture/json', RequestInterface::METHOD_POST);
    }


    /**
     * @return array
     */
    public function getPathParameters(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getBodyParameters(): array
    {
        return ['transactionId' => $this->transactionId];
    }

    /**
     * @return PayOrder
     * @throws PayException
     */
    public function start(): PayOrder
    {
        $this->config->setCore('https://rest-api.pay.nl');
        $this->config->setVersion(18);
        return parent::start();
    }
}
