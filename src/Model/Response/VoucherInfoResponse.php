<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Response;

use PayNL\Sdk\Model\ModelInterface;
use PayNL\Sdk\Model\Amount;

/**
 * Class VoucherPaymentResponse
 *
 * @package PayNL\Sdk\Model
 */
class VoucherInfoResponse implements ModelInterface
{
    /**
     * @var object
     */
    protected $integration;

    /**
     * @var amount
     */
    protected $amount;

    /**
     * @var string
     */
    protected $expiresAt;

    /**
     * @var object
     */
    protected $brand;

    /**
     * @return object
     */
    public function getIntegration(): object
    {
        return $this->integration;
    }

    /**
     * @param object $integration
     * @return $this
     */
    public function setIntegration(object $integration): self
    {
        $this->integration = $integration;
        return $this;
    }

    /**
     * @return Amount
     */
    public function getAmount(): Amount
    {
        return $this->amount;
    }

    /**
     * @param Amount $amount
     * @return $this
     */
    public function setAmount(Amount $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getExpiresAt(): string
    {
        return (string) $this->expiresAt;
    }

    /**
     * @param string $expiresAt
     * @return $this
     */
    public function setExpiresAt(string $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return object
     */
    public function getBrand(): object
    {
        return $this->brand;
    }

    /**
     * @param object $brand
     * @return $this
     */
    public function setBrand(object $brand): self
    {
        $this->brand = $brand;
        return $this;
    }
}
