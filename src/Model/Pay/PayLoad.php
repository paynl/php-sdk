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
        $this->reference = $payload['reference'];
        $this->action = $payload['action'];
        $this->paymentProfile = (int)$payload['payment_profile'];
        $this->payOrderId = $payload['pay_order_id'];
        $this->orderId = $payload['order_id'];
        $this->internalStateId = $payload['internal_state_id'];
        $this->internalStateName = (string)$payload['internal_state_name'];
        $this->checkoutData = (array)$payload['checkout_data'];
        $this->fullPayLoad = (array)$payload['full_payload'];
    }


    public function getFullPayLoad(): array
    {
        return $this->fullPayLoad;
    }




    public function getInternalStateId(): int
    {
        return $this->internalStateId;
    }

    public function getInternalStateName(): string
    {
        return $this->internalStateName;
    }

}