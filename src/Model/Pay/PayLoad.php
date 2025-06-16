<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Pay;

class PayLoad
{
    protected string $type;
    protected int $amount;
    protected string $currency;
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
    protected string $extra1;
    protected string $extra2;
    protected string $extra3;

    /**
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->type = (string)($payload['type'] ?? '');
        $this->amount = (int)($payload['amount'] ?? 0);
        $this->currency = (string)($payload['currency'] ?? '');
        $this->amountCap = (float)($payload['amount_cap'] ?? 0);
        $this->amountAuth = (float)($payload['amount_auth'] ?? 0);
        $this->reference = (string)($payload['reference'] ?? '');
        $this->action = (string)($payload['action'] ?? '');
        $this->paymentProfile = (int)($payload['payment_profile'] ?? 0);
        $this->payOrderId = (string)($payload['pay_order_id'] ?? '');
        $this->orderId = (string)($payload['order_id'] ?? '');
        $this->internalStateId = (int)($payload['internal_state_id'] ?? 0);
        $this->internalStateName = (string)($payload['internal_state_name'] ?? '');
        $this->checkoutData = is_array($payload['checkout_data'] ?? null) ? $payload['checkout_data'] : [];
        $this->fullPayLoad = is_array($payload['full_payload'] ?? null) ? $payload['full_payload'] : [];

        $this->extra1 = (string)($this->fullPayLoad['extra1'] ?? '');
        $this->extra2 = (string)($this->fullPayLoad['extra2'] ?? '');
        $this->extra3 = (string)($this->fullPayLoad['extra3'] ?? '');
    }

    /**
     * @return string
     */
    public function getExtra1(): string
    {
        return $this->extra1;
    }

    /**
     * @return string
     */
    public function getExtra2(): string
    {
        return $this->extra2;
    }

    /**
     * @return string
     */
    public function getExtra3(): string
    {
        return $this->extra3;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return array
     */
    public function getFullPayLoad(): array
    {
        return $this->fullPayLoad;
    }

    /**
     * @return integer
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
     * @return integer
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
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
     * @return integer
     */
    public function getPaymentProfile(): int
    {
        return $this->paymentProfile;
    }

    /**
     * @return array
     */
    public function getCheckoutData(): array
    {
        return $this->checkoutData;
    }

    /**
     * @return boolean
     */
    public function isFastCheckout(): bool
    {
        return strtolower($this->getType()) === 'payment_based_checkout';
    }

    
    /**
     * @return bool
     */
    function isTguTransaction(): bool
    {
        $id = $this->getPayOrderId()[0] ?? null;
        return ctype_digit($id) && (int)$id > 3;
    }

}
