<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Response;

use PayNL\Sdk\Model\ModelInterface;
use PayNL\Sdk\Model\Amount;

/**
 * Class VoucherCaptureResponse
 *
 * @package PayNL\Sdk\Model
 */
class VoucherCaptureResponse implements ModelInterface
{

    /**
     * @var string
     */
    protected $id;

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
    protected $orderId;

    /**
     * @var string
     */
    protected $paymentUrl;

    /**
     * @var Amount
     */
    protected $amount;

    /**
     * @var string
     */
    protected $created;

    /**
     * @var string
     */
    protected $modified;

    /**
     * @return string
     */
    public function getId(): string
    {
        return (string) $this->id;
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
    public function getDescription(): string
    {
        return (string) $this->description;
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
        return (string) $this->reference;
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
    public function getOrderId(): string
    {
        return (string) $this->orderId;
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
    public function getPaymentUrl(): string
    {
        return (string) $this->paymentUrl;
    }

    /**
     * @param string $paymentUrl
     * @return $this
     */
    public function setPaymentUrl(string $paymentUrl): self
    {
        $this->paymentUrl = $paymentUrl;
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
    public function getCreated(): string
    {
        return (string) $this->created;
    }

    /**
     * @param string $created
     * @return $this
     */
    public function setCreatedAt(string $created): self
    {
        $this->createdAt = $created;
        return $this;
    }

    /**
     * @return string
     */
    public function getModified(): string
    {
        return (string) $this->modified;
    }

    /**
     * @param string $modified
     * @return $this
     */
    public function setModified(string $modified): self
    {
        $this->modified = $modified;
        return $this;
    }

}
