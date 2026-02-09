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
class VoucherPaymentResponse implements ModelInterface
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
    protected $description;

    /**
     * @var string
     */
    protected $reference;

    /**
     * @var string
     */
    protected $manualTransferCode;

    /**
     * @var Amount
     */
    protected $amount;

    /**
     * @var string
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $expiresAt;

    /**
     * @var object
     */
    protected $integration;

    /**
     * @var object
     */
    protected $links;

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
    public function getManualTransferCode(): string
    {
        return (string) $this->manualTransferCode;
    }

    /**
     * @param string $manualTransferCode
     * @return $this
     */
    public function setManualTransferCode(string $manualTransferCode): self
    {
        $this->manualTransferCode = $manualTransferCode;
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
    public function getcreatedAt(): string
    {
        return (string) $this->createdAt;
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setcreatedAtAt(string $createdAt): self
    {
        $this->createdAtAt = $createdAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getexpiresAt(): string
    {
        return (string) $this->expiresAt;
    }

    /**
     * @param string $expiresAt
     * @return $this
     */
    public function setexpiresAt(string $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return boolean
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
     * @return string
     */
    public function getLinks(): object
    {
        return $this->links;
    }

    /**
     * @param object $links
     * @return $this
     */
    public function setLinks(object $links): self
    {
        $this->links = $links;
        return $this;
    }

}
