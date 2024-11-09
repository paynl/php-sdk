<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Pay;

use PayNL\Sdk\Model\Pay\PayStatus;

class PayOrder
{
    private int $stateId = 0;
    private array $processedPayload;
    private string $message;
    private array $payload = [];

    private $id;
    private $uuid;
    private $amount;
    private $status;
    private $orderId;
    private $receipt;
    private $payments;
    private $reference;
    private $description;
    private $integration;
    private $checkoutData;
    private $capturedAmount;
    private $authorizedAmount;
    private $manualTransferCode;

    /**
     * @var mixed
     */
    private $paymentProfileId;

    /**
     * @param $payload
     */
    public function __construct($payload)
    {
        if (!empty($payload)) {
            foreach ($payload as $_key => $_val) {
                $method = 'set' . ucfirst($_key);
                if (method_exists($this, $method)) {
                    $this->$method($_val);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function failed()
    {
        return $this->stateId === 0;
    }

    /**
     * @param $p
     * @return $this
     */
    public function setPaymentProfileId($p)
    {
        $this->paymentProfileId = $p;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentProfileId()
    {
        return $this->paymentProfileId;
    }

    /**
     * @return int|mixed
     */
    public function getLastUsedProfileId()
    {
        $lastUsed = 0;
        foreach ($this->payload['payments'] as $payment) {
            $lastUsed = $payment['paymentMethod']['id'];
        }

        return $lastUsed;
    }

    /**
     * @return bool
     */
    public function isFastCheckout()
    {
        return !empty($this->processedPayload['checkoutData'] ?? []);
    }

    /**
     * @return array
     */
    public function getCheckoutData()
    {
        return (array)$this->processedPayload['checkoutData'] ?? [];
    }

    /**
     * @return int
     */
    public function getStateId(): int
    {
        return $this->stateId;
    }

    /**
     * @param  $stateId
     * @return void
     */
    public function setStateId($stateId): void
    {
        $this->stateId = $stateId;
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
     * @return void
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return array
     */
    public function getProcessedPayload(): array
    {
        return $this->processedPayload;
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return $this->stateId === PayStatus::PAID;
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return $this->stateId === PayStatus::PENDING;
    }

    /**
     * @return bool
     */
    public function isPartialPayment()
    {
        return $this->stateId === PayStatus::PARTIAL_PAYMENT;
    }

    /**
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->stateId === PayStatus::AUTHORIZE;
    }

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->stateId === PayStatus::CANCEL;
    }

    /**
     * @return bool
     */
    public function isRefundedFully()
    {
        return $this->stateId === PayStatus::REFUND;
    }

    /**
     * @return bool
     */
    public function isRefundedPartial()
    {
        return $this->stateId === PayStatus::PARTIAL_REFUND;
    }

    /**
     * @return bool
     */
    public function isBeingVerified()
    {
        return $this->stateId === PayStatus::VERIFY;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return (array)$this->payload;
    }

    /**
     * @param array $payload
     * @return void
     */
    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param mixed $uuid
     */
    public function setUuid($uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     */
    public function setOrderId($orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return mixed
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param mixed $payments
     */
    public function setPayments($payments): void
    {
        $this->payments = $payments;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCapturedAmount()
    {
        return $this->capturedAmount;
    }

    /**
     * @param mixed $capturedAmount
     */
    public function setCapturedAmount($capturedAmount): void
    {
        $this->capturedAmount = $capturedAmount;
    }

}