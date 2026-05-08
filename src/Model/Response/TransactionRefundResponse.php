<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Response;

use PayNL\Sdk\Model\ModelInterface;
use PayNL\Sdk\Model\Amount;

/**
 * Class TransactionRefundResponse
 *
 * @package PayNL\Sdk\Model
 */
class TransactionRefundResponse implements ModelInterface
{
    /**
     * @var string
     */
    protected $orderId;

    /**
     * @var string
     */
    protected $transactionId;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $processDate;

    /**
     * @var Amount
     */
    protected $amount;

    /**
     * @var Amount
     */
    protected $amountRefunded;

    /**
     * @var array
     */
    protected $refundedTransactions;

    /**
     * @var string
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $createdBy;

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
    public function getTransactionId(): string
    {
        return (string)$this->transactionId;
    }

    /**
     * @param string $transactionId
     * @return $this
     */
    public function setTransactionId(string $transactionId): self
    {
        $this->transactionId = $transactionId;
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
    public function getProcessDate(): string
    {
        return (string)$this->processDate;
    }

    /**
     * @param string $processDate
     * @return $this
     */
    public function setProcessDate(string $processDate): self
    {
        $this->processDate = $processDate;
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
    public function getRefundedTransactions(): array
    {
        return $this->refundedTransactions;
    }

    /**
     * @param array $refundedTransactions
     * @return $this
     */
    public function setRefundedTransactions(array $refundedTransactions): self
    {
        $this->refundedTransactions = $refundedTransactions;
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

}
