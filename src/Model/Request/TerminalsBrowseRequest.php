<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Terminals;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class TerminalsBrowseRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class TerminalsBrowseRequest extends RequestData
{
    protected string $merchantCode;
    protected string $excludeMerchantCode;

    /**
     * @param string $merchantCode
     * @param string $excludeMerchantCode
     */
    public function __construct(?string $merchantCode = '', ?string $excludeMerchantCode = '')
    {
        $this->setMerchantCode($merchantCode);
        $this->setExcludeMerchantCode($excludeMerchantCode);
        parent::__construct('TerminalsBrowse', '/terminals', RequestInterface::METHOD_GET);
    }

    /**
     * @param string $merchantCode
     * @return $this
     */
    public function setMerchantCode(string $merchantCode): self
    {
        $this->merchantCode = $merchantCode;
        return $this;
    }

    /**
     * @param string $excludeMerchantCode
     * @return $this
     */
    public function setExcludeMerchantCode(string $excludeMerchantCode): self
    {
        $this->excludeMerchantCode = $excludeMerchantCode;
        return $this;
    }

    /**
     * @return array|null[]
     */
    public function getPathParameters(): array
    {
        $parameters = [];

        if (!empty($this->merchantCode)) {
            $parameters['merchant[eq]'] = $this->merchantCode;
        }
        if (!empty($this->excludeMerchantCode)) {
            $parameters['merchant[neq]'] = $this->excludeMerchantCode;
        }

        return $parameters;
    }

    /**
     * @return array
     */
    public function getBodyParameters(): array
    {
        return [];
    }

    /**
     * @return Terminals
     * @throws PayException
     */
    public function start(): Terminals
    {
        $this->config->setCore('https://rest.pay.nl');
        $this->config->setVersion(2);
        return parent::start();
    }
}
