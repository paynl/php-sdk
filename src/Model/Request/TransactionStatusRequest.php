<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Config\Config;
use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Request\RequestInterface;
use PayNL\Sdk\Helpers\StaticCacheTrait;
use PayNL\Sdk\Util\PayCache;

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
     * @param string $orderId
     */
    public function __construct(string $orderId)
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
     * @throws \Exception
     */
    public function start(): PayOrder
    {
        $cacheKey = 'transaction_status_' . md5(json_encode([$this->config->getUsername(), $this->orderId]));

        if ($this->hasStaticCache($cacheKey)) {
            return $this->getStaticCacheValue($cacheKey);
        }

        if ($this->config->isCacheEnabled()) {
            $cache = new PayCache();
            return $cache->get($cacheKey, function () use ($cacheKey) {
                return $this->staticCache($cacheKey, function () {
                    $this->config->setCore('https://rest.pay.nl');
                    return parent::start();
                });
            }, 3); # 3 seconds file caching
        }

        return $this->staticCache($cacheKey, function () {
            $this->config->setCore('https://rest.pay.nl');
            return parent::start();
        });
    }
}
