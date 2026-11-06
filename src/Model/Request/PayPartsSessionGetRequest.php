<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Model\Response\PayPartsSessionGetResponse;
use PayNL\Sdk\Request\RequestInterface;
use PayNL\Sdk\Request\RequestData;

/**
 * Retrieve a PayParts session, including its order status when available.
 */
class PayPartsSessionGetRequest extends RequestData
{
    public const API_URL = 'https://parts.pay.nl';
    public const ZERO_API_URL = 'https://zero-parts.pay.nl';

    private bool $zero = false;

    private string $sessionId = '';

    /**
     * Use the Zero pre-production environment, or pass false to use production.
     * @param boolean $useZero
     * @return $this
     */
    public function useZero(bool $useZero = true): self
    {
        $this->zero = $useZero;
        return $this;
    }

    /**
     * Initialize the session endpoint.
     */
    public function __construct()
    {
        parent::__construct('PayPartsSessionGet', 'session/%sessionId%', RequestInterface::METHOD_GET);
    }

    /**
     * Set the ID returned by session creation.
     * @param string $sessionId
     * @return $this
     */
    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = trim($sessionId);
        return $this;
    }

    /**
     * Encode the session ID as a single URL path segment.
     * @return array
     * @throws PayException
     */
    public function getPathParameters(): array
    {
        if ($this->sessionId === '') {
            throw new PayException('Required param `sessionId` is empty', 0, 0);
        }
        return ['sessionId' => rawurlencode($this->sessionId)];
    }

    /**
     * A GET request has no body.
     * @return array
     */
    public function getBodyParameters(): array
    {
        return [];
    }

    /**
     * Retrieve the session details.
     * @return PayPartsSessionGetResponse
     * @throws PayException
     */
    public function start(): PayPartsSessionGetResponse
    {
        $this->config = clone $this->config;
        // Use the PayParts environment by default; an explicit failover URL may override it.
        $this->config->setCore($this->zero ? self::ZERO_API_URL : self::API_URL);
        $this->config->setVersion(1);
        return parent::start();
    }
}
