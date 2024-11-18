<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Request;

use PayNL\Sdk\Exception\PayException;
use PayNL\Sdk\Request\RequestData;
use PayNL\Sdk\Model\Pay\PayOrder;
use PayNL\Sdk\Request\RequestInterface;

/**
 * Class OrderCaptureRequest
 *
 * @package PayNL\Sdk\Model\Request
 */
class OrderCaptureRequest extends RequestData
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

        parent::__construct('OrderCapture', '/orders/%transactionId%/capture', RequestInterface::METHOD_PATCH);
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

        if (!is_null($this->amount)) {
            $parameters['amount'] = $this->amount;
        }
        if (!is_null($this->productId)) {
            $parameters['products'][] = [
              'id' => $this->productId,
              'quantity' => $this->quantity,
            ];
        }
        return $parameters;
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
        if ($this->mode == 'amount') {
            $this->uri = '/orders/%transactionId%/capture/amount';
        } elseif ($this->mode == 'product') {
            $this->uri = '/orders/%transactionId%/capture/products';
        }
        return parent::start();
    }
}