<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Pay;

class PayLoad
{
    protected float $amount;
    protected float $amountCap;
    protected float $amountAuth;
    protected string $reference;
    protected string $action;
    protected int $paymentProfile;
    protected string $payOrderId;
    protected string $orderId;
    protected int $internalStateId;
    protected string $internalStateName;
    protected array $checkoutData;
    protected array $fullPayLoad = [];

    /**
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->amount = (float)$payload['amount'];
        $this->amountCap = (float)$payload['amount_cap'];
        $this->amountAuth = (float)$payload['amount_auth'];
        $this->reference = (string)$payload['reference'];
        $this->action = (string)$payload['action'];
        $this->paymentProfile = (int)$payload['payment_profile'];
        $this->payOrderId = (string)$payload['pay_order_id'];
        $this->orderId = (string)$payload['order_id'];
        $this->internalStateId = (int)$payload['internal_state_id'];
        $this->internalStateName = (string)$payload['internal_state_name'];
        $this->checkoutData = (array)$payload['checkout_data'];
        $this->fullPayLoad = (array)$payload['full_payload'];
    }

    /**
     * @return array
     */
    public function getFullPayLoad(): array
    {
        return $this->fullPayLoad;
    }

    /**
     * @return int
     */
    public function getInternalStateId(): int
    {
        return $this->internalStateId;
    }

    /**
     * @return string
     */
    public function getInternalStateName(): string
    {
        return $this->internalStateName;
    }

    /**
     * @return string
     */
    public function getPayOrderId(): string
    {
        return $this->payOrderId;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getAmountCap(): float
    {
        return $this->amountCap;
    }

    /**
     * @return float
     */
    public function getAmountAuth(): float
    {
        return $this->amountAuth;
    }

    /**
     * @return int
     */
    public function getPaymentProfile(): int
    {
        return $this->paymentProfile;
    }

}