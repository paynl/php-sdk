<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Request\RequestInterface;
use PayNL\Sdk\Helpers\StaticCacheTrait;

/**
 * Class TransactionStatusRequest
 * Request the status of a transaction using this method.
 *
 * @package PayNL\Sdk\Model\Request
 */
class TransactionStatusRequest extends RequestData
{
    use StaticCacheTrait;

    private string $orderId;

    /**
     * @param $orderid
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
        parent::__construct('TransactionStatus', '/transactions/%transactionId%/status', RequestInterface::METHOD_GET);
    }

    /**
     * @return string[]
     */
    public function getPathParameters(): array
    {
        return [
            'transactionId' => $this->orderId
        ];
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
    public function stgtart(): PayOrder
    {
        # Always use rest.pay.nl for this status request
        $this->config->setCore('https://rest.pay.nl');
        return parent::start();
    }


    /**
     * @return PayOrder
     * @throws \Exception
     */
    public function start(): PayOrder
    {
        $cacheKey = 'transaction_status_' . md5(json_encode([$this->config->getUsername(), $this->orderId]));

        return $this->staticCache($cacheKey, function () {
            $this->config->setCore('https://rest.pay.nl');
            return parent::start();
        });
    }
}