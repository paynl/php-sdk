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
     * @var string
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $expiresAt;

    /**
     * @var Amount
     */
    protected $amount;

    /**
     * @var array
     */
    protected $integration;

    /**
     * @var array
     */
    protected $links;


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
    public function getId(): string
    {
        return $this->id;
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
    public function getOrderId(): string
    {
        return $this->orderId;
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
    public function getDescription(): string
    {
        return $this->description;
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
    public function getReference(): string
    {
        return $this->reference;
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
     * @return string
     */
    public function getManualTransferCode(): string
    {
        return $this->manualTransferCode;
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
    public function getCreatedAt(): string
    {
        return $this->createdAt;
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
    public function getExpiresAt(): string
    {
        return $this->expiresAt;
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
     * @return float|integer
     */
    public function getAmount(): float|int
    {
        return $this->amount->getValue() / 100;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return (string) $this->amount->getCurrency();
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
     * @return array
     */
    public function getIntegration(): array
    {
        return $this->integration;
    }

    /**
     * @return boolean
     */
    public function getTest(): bool
    {
        return $this->integration['test'] ?? false;
    }

    /**
     * @return string
     */
    public function getPointOfInteraction(): string
    {
        return $this->integration['pointOfInteraction'] ?? '';
    }

    /**
     * @param array $links
     * @return $this
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;
        return $this;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @return string
     */
    public function getStatusLink(): string
    {
        return $this->links['status'] ?? '';
    }

    /**
     * @return string
     */
    public function getRedirectLink(): string
    {
        return $this->links['redirect'] ?? '';
    }
}
