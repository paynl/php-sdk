<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Response\OrderApproveResponse;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class OrderApproveRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class OrderApproveRequest extends RequestData
{
    private string $transactionId;

    /**
     * @param $transactionId
     */
    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
        parent::__construct('orderApprove', '/orders/%transactionId%/approve', RequestInterface::METHOD_PATCH);
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
     * @return OrderApproveResponse
     * @throws PayException
     */
    public function start(): OrderApproveResponse
    {
        return parent::start();
    }
}