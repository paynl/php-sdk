<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Response\OrderDeclineResponse;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class OrderDeclineRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class OrderDeclineRequest extends RequestData
{
    private string $transactionId;

    /**
     * @param $transactionId
     */
    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
        parent::__construct('orderDecline', '/orders/%transactionId%/decline', RequestInterface::METHOD_PATCH);
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
     * @return OrderDeclineResponse
     * @throws PayException
     */
    public function start(): OrderDeclineResponse
    {
        return parent::start();
    }
}