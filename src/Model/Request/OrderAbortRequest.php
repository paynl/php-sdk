<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class OrderAbortRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class OrderAbortRequest extends RequestData
{
    private string $transactionId;

    /**
     * @param $transactionId
     */
    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
        parent::__construct('OrderAbort', '/orders/%transactionId%/abort', RequestInterface::METHOD_PATCH);
    }

    /**
     * @return string[]
     */
    public function getPathParameters(): array
    {
        return ['transactionId' => $this->transactionId];
    }

    /**
     * @return array
     */
    public function getBodyParameters(): array
    {
        return [];
    }

    /**
     * @return PayOrder
     * @throws PayException
     */
    public function start(): PayOrder
    {
        return parent::start();
    }
}