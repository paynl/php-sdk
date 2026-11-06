<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Response;

/**
 * Session details with the original API field names and monetary values in cents.
 */
class PayPartsSessionGetResponse extends PayPartsSessionCreateResponse
{
    /**
     * @var array
     */
    protected array $amount = [];

    /**
     * Set the amount returned by the API.
     */
    public function setAmount(array $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get the amount.
     */
    public function getAmount(): array
    {
        return $this->amount;
    }

    /**
     * @var array
     */
    protected array $customer = [];

    /**
     * Set the customer returned by the API.
     */
    public function setCustomer(array $customer): self
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * Get the customer.
     */
    public function getCustomer(): array
    {
        return $this->customer;
    }

    /**
     * @var array
     */
    protected array $order = [];

    /**
     * Set the order returned by the API.
     */
    public function setOrder(array $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get the order.
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * @var array
     */
    protected ?array $orderStatus = null;

    /**
     * Set the orderStatus returned by the API.
     */
    public function setOrderStatus(?array $orderStatus): self
    {
        $this->orderStatus = $orderStatus;
        return $this;
    }

    /**
     * Get the orderStatus; null until an order exists.
     */
    public function getOrderStatus(): ?array
    {
        return $this->orderStatus;
    }
}
