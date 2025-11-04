<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class OrderUpdateRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class OrderUpdateRequest extends RequestData
{
    private string $transactionId;
    private string $description = '';
    private string $reference = '';

    /**
     * @param $transactionId
     */
    public function __construct(string $transactionId)
    {
        $this->transactionId = $transactionId;
        $this->description = '';
        parent::__construct('OrderUpdate', '/orders/%transactionId%', RequestInterface::METHOD_PATCH);
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $reference
     * @return $this
     */
    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
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
        $parameters = [];

        if (!empty($this->description)) {
            $parameters['description'] = $this->description;
        }
        if (!empty($this->reference)) {
            $parameters['reference'] = $this->reference;
        }
        return $parameters;
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