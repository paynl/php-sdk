<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class OrderCaptureLegacyRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class OrderCaptureLegacyRequest extends RequestData
{
    private string $transactionId;
    private ?int $amount = null;
    private $productId;
    private $quantity;
    private $mode;

    /**
     * @param $transactionId
     * @param float|null $amount
     */
    public function __construct($transactionId, float $amount = null)
    {
        $this->transactionId = $transactionId;
        if (!empty($amount)) {
            $this->setAmount($amount);
        }

        parent::__construct('OrderCaptureLegacy', '/transaction/capture/json', RequestInterface::METHOD_POST);
    }

    /**
     * @param $productId
     * @param $quantity
     * @return $this
     */
    public function setProduct($productId, $quantity): self
    {
        $this->mode = 'product';
        $this->productId = $productId;
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return array
     */
    public function getPathParameters(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getBodyParameters(): array
    {
        return ['transactionId' => $this->transactionId];
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount(float $amount): self
    {
        $this->mode = 'amount';
        $this->amount = (int)round($amount * 100);
        return $this;
    }

    /**
     * @return PayOrder
     * @throws PayException
     */
    public function start(): PayOrder
    {
        $this->config->setCore('https://rest-api.pay.nl');
        $this->config->setVersion(18);
        return parent::start();
    }

}