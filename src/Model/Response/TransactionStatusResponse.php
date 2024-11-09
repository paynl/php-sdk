<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Response;

use PayNL\Sdk\Model\ModelInterface;
use PayNL\Sdk\Model\Amount;

/**
 * Class TransactionStatusResponse
 *
 * @package PayNL\Sdk\Model
 */
class TransactionStatusResponse implements ModelInterface
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
        return (string) $this->ipAddress ?? '';
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
     * @return float|int
     */
    public function getAmount()
    {
        return $this->amount->getValue() / 100;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return (string)$this->amount->getCurrency();
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
     * @param Amount $amountConverted
     * @return $this
     */
    public function setAmountConverted(Amount $amountConverted): self
    {
        $this->amountConverted = $amountConverted;
        return $this;
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

    /**
     * @return array
     */
    public function getPaymentMethod(): array
    {
        return $this->paymentMethod;
    }

    /**
     * @param array $paymentMethod
     */
    public function setPaymentMethod(array $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
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
     */
    public function setStatus(array $status): void
    {
        $this->status = $status;
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
     */
    public function setPaymentData(array $paymentData): void
    {
        $this->paymentData = $paymentData;
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return ($this->status['action'] ?? '') === 'PAID';
    }

    /**
     * Checks whether the payment is authorized
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->status['code'] == 95;
    }

    /**
     * Checks whether the payment is being verified
     *
     * @return bool
     */
    public function isBeingVerified(): bool
    {
        return ($this->status['action'] ?? '')  === 'VERIFY';
    }

    /**
     * Checks whether the payment is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return in_array( ($this->status['action'] ?? ''), ['PENDING', 'VERIFY']);
    }

    /**
     * Check whether the status of the transaction is chargeback
     *
     * @return bool
     */
    public function isChargeBack(): bool
    {
        return ($this->status['action'] ?? '')  === 'CHARGEBACK';
    }

    /**
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status['code'] < 0;
    }

    /**
     * @param bool $allowPartialRefunds
     *
     * @return bool
     */
    public function isRefunded(bool $allowPartialRefunds = true): bool
    {
        if (($this->status['action'] ?? '')  === 'REFUND') {
            return true;
        }

        if ($allowPartialRefunds && ($this->status['action'] ?? '')  === 'PARTIAL_REFUND') {
            return true;
        }

        return false;
    }

    /**
     * Check whether the payment is partial refunded
     *
     * @return bool
     */
    public function isPartiallyRefunded(): bool
    {
        return ($this->status['action'] ?? '')  === 'PARTIAL_REFUND';
    }

    /**
     * Check whether the payment is a partial payment.
     *
     * @return bool
     */
    public function isPartialPayment(): bool
    {
        return ($this->status['action'] ?? '')  === 'PARTIAL_PAYMENT';
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return (int)$this->status['code'];
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return (string)($this->status['action'] ?? '') ;
    }

    /**
     * @return int
     */
    public function getPaymentProfileId(): int
    {
        return $this->getPaymentMethod()['id'];
    }

    /**
     * @return float|int
     */
    public function getAmountConverted()
    {
        return $this->amountConverted->getValue() / 100;
    }

    /**
     * @return string
     */
    public function getAmountConvertedCurrency(): string
    {
        return (string)$this->amountConverted->getCurrency();
    }

    /**
     * @return float|int
     */
    public function getAmountPaid()
    {
        return $this->amountPaid->getValue() / 100;
    }

    /**
     * @return string
     */
    public function getAmountPaidCurrency(): string
    {
        return (string)$this->amountPaid->getCurrency();
    }

    /**
     * @return float|int
     */
    public function getAmountRefunded()
    {
        return $this->amountRefunded->getValue() / 100;
    }

    /**
     * @return string
     */
    public function getAmountRefundedCurrency(): string
    {
        return (string)$this->amountRefunded->getCurrency();
    }
}