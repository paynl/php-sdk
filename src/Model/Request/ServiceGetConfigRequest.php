<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Response\ServiceGetConfigResponse;
use PayNL\Sdk\Request\RequestInterface;
use PayNL\Sdk\Util\PayCache;

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
     * @var array cached result
     */
    private static $cache = array();

    /**
     * @return ServiceGetConfigResponse
     * @throws PayException
     */
    public function start(): ServiceGetConfigResponse
    {
        $cacheKey = 'service_getconfig_' . md5(json_encode([$this->config->getUsername(), $this->config->getPassword(), $this->serviceId]));

        # First check static cache
        if (isset(self::$cache[$cacheKey])) {
            if (self::$cache[$cacheKey] instanceof \Exception) {
                throw self::$cache[$cacheKey];
            }
            return self::$cache[$cacheKey];
        }

        # Then check file-based cache
        if ($this->config->isCacheEnabled()) {
            $cache = new PayCache();

            return $cache->get($cacheKey, function () use ($cacheKey) {
                try {
                    $result = $this->startAPI();
                    self::$cache[$cacheKey] = $result;
                    return $result;
                } catch (\Exception $e) {
                    self::$cache[$cacheKey] = $e;
                    throw $e;
                }
            }, 5); // 5 seconden caching
        }

        try {
            $result = $this->startAPI();
            self::$cache[$cacheKey] = $result;
            return $result;
        } catch (\Exception $e) {
            self::$cache[$cacheKey] = $e;
            throw $e;
        }
    }

    /**
     * @return ServiceGetConfigResponse
     * @throws PayException
     */
    private function startAPI(): ServiceGetConfigResponse
    {
        $this->config->setCore('https://rest.pay.nl');
        $this->config->setVersion(2);
        return parent::start();
    }
}