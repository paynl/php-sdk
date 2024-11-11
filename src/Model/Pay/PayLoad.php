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

    public function getPayOrderId(): string
    {
        return $this->payOrderId;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

}