<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Response\TransactionStatusResponse;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class TransactionStatusRequest
 * Request the status of a transaction using this method.
 *
 * @package PayNL\Sdk\Model\Request
 */
class TransactionStatusRequest extends RequestData
{
    private string $orderId;

    /**
     * @param $orderid
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        parent::__construct('TransactionStatus', '/transactions/%transactionId%/status', RequestInterface::METHOD_GET);
    }

    public function getPathParameters(): array
    {
        return [
            'transactionId' => $this->orderId
        ];
    }

    public function getBodyParameters(): array
    {
        return [];
    }

    /**
     * @return TransactionStatusResponse
     * @throws PayException
     */
    public function start(): TransactionStatusResponse
    {
        # Always use rest.pay.nl for this status request
        $this->config->setCore('https://rest.pay.nl');
        return parent::start();
    }
}