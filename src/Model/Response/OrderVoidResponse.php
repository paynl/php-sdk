<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Response;

use PayNL\Sdk\Model\ModelInterface;

use PayNL\Sdk\Model\Amount;

/**
 * Class OrderVoidResponse
 *
 * @package PayNL\Sdk\Model
 */
class OrderVoidResponse implements ModelInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $serviceCode;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $ipAddress;

    /**
     * @var Amount
     */
    protected $amount;

    /**
     * @var Amount
     */
    protected $amountConverted;

    /**
     * @var Amount
     */
    protected $amountPaid;

    /**
     * @var Amount
     */
    protected $amountRefunded;

    /**
     * @var array
     */
    protected $status;

    /**
     * @var array
     */
    protected $paymentData;

    /**
     * @var array
     */
    protected $paymentMethod;

    /**
     * @var array
     */
    protected $integration;

    /**
     * @var string
     */
    protected $expiresAt;

    /**
     * @var string
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $createdBy;

    /**
     * @var string
     */
    protected $modifiedAt;

    /**
     * @var string
     */
    protected $modifiedBy;

    /**
     * @var string
     */
    protected $deletedAt;

    /**
     * @var string
     */
    protected $deletedBy;


    /**
     * @return string
     */
    public function getId(): string
    {
        return (string)$this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return (string)$this->orderId;
    }

    /**
     * @param string $orderId
     * @return $this
     */
    public function setOrderId(string $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceCode(): string
    {
        return (string)$this->serviceCode;
    }

    /**
     * @param string $serviceCode
     * @return $this
     */
    public function setServiceCode(string $serviceCode): self
    {
        $this->serviceCode = $serviceCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return (string)$this->description;
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
     * @return string
     */
    public function getReference(): string
    {
        return (string)$this->reference;
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
     * @return string
     */
    public function getIpAddress(): string
    {
        return (string)$this->ipAddress;
    }

    /**
     * @param string $ipAddress
     * @return $this
     */
    public function setIpAddress(string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
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
     * @return Amount
     */
    public function getAmountConverted(): Amount
    {
        return $this->amountConverted;
    }

    /**
     * @param Amount $amountConverted
     * @return $this
     */
    public function setAmountConverted(Amount $amountConverted): self
    {
        $this->amountConverted = $amountConverted;
        return $this;
    }

    /**
     * @return Amount
     */
    public function getAmountPaid(): Amount
    {
        return $this->amountPaid;
    }

    /**
     * @param Amount $amountPaid
     * @return $this
     */
    public function setAmountPaid(Amount $amountPaid): self
    {
        $this->amountPaid = $amountPaid;
        return $this;
    }

    /**
     * @return Amount
     */
    public function getAmountRefunded(): Amount
    {
        return $this->amountRefunded;
    }

    /**
     * @param Amount $amountRefunded
     * @return $this
     */
    public function setAmountRefunded(Amount $amountRefunded): self
    {
        $this->amountRefunded = $amountRefunded;
        return $this;
    }

    /**
     * @return array
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param array $status
     * @return $this
     */
    public function setStatus(array $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return array
     */
    public function getPaymentData(): array
    {
        return $this->paymentData;
    }

    /**
     * @param array $paymentData
     * @return $this
     */
    public function setPaymentData(array $paymentData): self
    {
        $this->paymentData = $paymentData;
        return $this;
    }

    /**
     * @return array
     */
    public function getPaymentMethod(): array
    {
        return $this->paymentMethod;
    }

    /**
     * @param array $paymentMethod
     * @return $this
     */
    public function setPaymentMethod(array $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @return array
     */
    public function getIntegration(): array
    {
        return $this->integration;
    }

    /**
     * @param array $integration
     * @return $this
     */
    public function setIntegration(array $integration): self
    {
        $this->integration = $integration;
        return $this;
    }

    /**
     * @return string
     */
    public function getExpiresAt(): string
    {
        return (string)$this->expiresAt;
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
     * @return string
     */
    public function getCreatedAt(): string
    {
        return (string)$this->createdAt;
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedBy(): string
    {
        return (string)$this->createdBy;
    }

    /**
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getModifiedAt(): string
    {
        return (string)$this->modifiedAt;
    }

    /**
     * @param string $modifiedAt
     * @return $this
     */
    public function setModifiedAt(string $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getModifiedBy(): string
    {
        return (string)$this->modifiedBy;
    }

    /**
     * @param string $modifiedBy
     * @return $this
     */
    public function setModifiedBy(string $modifiedBy): self
    {
        $this->modifiedBy = $modifiedBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeletedAt(): string
    {
        return (string)$this->deletedAt;
    }

    /**
     * @param string $deletedAt
     * @return $this
     */
    public function setDeletedAt(string $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeletedBy(): string
    {
        return (string)$this->deletedBy;
    }

    /**
     * @param string $deletedBy
     * @return $this
     */
    public function setDeletedBy(string $deletedBy): self
    {
        $this->deletedBy = $deletedBy;
        return $this;
    }

}
