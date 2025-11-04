<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Pay;

use Exception;
use PayNL\Sdk\Model\ModelInterface;
use PayNL\Sdk\Model\Amount;

/**
 * Class PayOrder
 *
 * @package PayNL\Sdk\Model
 */
class PayOrder implements ModelInterface
{
    /**
     * @var string
     */
    protected string $id;

    /**
     * @var string
     */
    private ?string $type = null;

    /**
     * @var string
     */
    protected string $serviceId;

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
    protected $orderId;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var string
     */
    protected $customerKey;

    /**
     * @var array
     */
    protected $status;

    /**
     * @var string
     */
    protected $receipt;

    /**
     * @var array
     */
    protected $integration;

    /**
     * @var Amount
     */
    protected $amount;

    /**
     * @var Amount
     */
    protected $authorizedAmount;

    /**
     * @var Amount
     */
    protected $capturedAmount;

    /**
     * @var array
     */
    protected $checkoutData;

    /**
     * @var array
     */
    private ?array $payments = null;

    /**
     * @var string
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $createdBy;

    /**
     * @var string
     */
    protected $modifiedAt;

    /**
     * @var string
     */
    protected $modifiedBy;

    /**
     * @var string
     */
    protected $expiresAt;

    /**
     * @var string
     */
    protected $completedAt;

    /**
     * @var Amount
     */
    protected $amountRefunded;

    /**
     * @var array
     */
    protected $links;

    /**
     * @param array|null $payload
     */
    public function __construct(?array $payload = null)
    {
        if (!empty($payload['object']) && is_array($payload['object'])) {
            foreach ($payload['object'] as $_key => $_val) {
                if (in_array($_key, ['amount', 'capturedAmount', 'authorizedAmount'])) {
                    continue;
                }
                $method = 'set' . ucfirst((string)$_key);
                if (method_exists($this, $method) && !is_null($_val)) {
                    $this->$method($_val);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getAmountRefunded()
    {
        if (!empty($this->amountRefunded) && $this->amountRefunded instanceof Amount) {
            return $this->amountRefunded->getValue() / 100;
        }
        return null;
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
     * @return string
     */
    public function getType(): string
    {
        return (string)$this->type;
    }

    /**
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFastCheckout(): bool
    {
        return strtolower($this->getType()) === 'payment_based_checkout';
    }

    /**
     * @return array
     */
    public function getFastCheckoutData(): array
    {
        return $this->getCheckoutData();
    }

    /**
     * @return integer
     */
    public function getStatusCode(): int
    {
        return (int)($this->status['code'] ?? 0);
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return (string)($this->status['action'] ?? '');
    }


    /**
     * @return string
     */
    public function getId(): string
    {
        return (string)$this->id;
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
    public function getServiceId(): string
    {
        return (string)$this->serviceId;
    }

    /**
     * @param string $serviceId
     * @return $this
     */
    public function setServiceId(string $serviceId): self
    {
        $this->serviceId = $serviceId;
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
    public function getReference(): string
    {
        return (string)$this->reference;
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
    public function getManualTransferCode(): string
    {
        return (string)$this->manualTransferCode;
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
    public function getUuid(): string
    {
        return (string)$this->uuid;
    }

    /**
     * @param string $uuid
     * @return $this
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerKey(): string
    {
        return (string)$this->customerKey;
    }

    /**
     * @param string $customerKey
     * @return $this
     */
    public function setCustomerKey(string $customerKey): self
    {
        $this->customerKey = $customerKey;
        return $this;
    }

    /**
     * @return array
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @param mixed $code
     * @param mixed $name
     * @return $this
     */
    public function setStatusCodeName(mixed $code, mixed $name): self
    {
        $this->status = ['code' => $code, 'action' => $name];
        return $this;
    }

    /**
     * @param array $status
     * @return $this
     */
    public function setStatus(array $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getReceipt(): string
    {
        return (string)$this->receipt;
    }

    /**
     * @param string $receipt
     * @return $this
     */
    public function setReceipt(string $receipt): self
    {
        $this->receipt = (string)$receipt;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTestmode(): bool
    {
        $testmodeEnabled = $this->integration['test'] ?? false;
        return $testmodeEnabled === true;
    }

    /**
     * @return array
     */
    public function getIntegration(): array
    {
        return $this->integration;
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
     * @return float|integer
     */
    public function getAmount()
    {
        return $this->amount->getValue() / 100;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return (string)$this->amount->getCurrency();
    }


    /**
     * @return mixed|null
     */
    public function getPaymentMethod()
    {
        return $this->payments[0]['paymentMethod']['id'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getCustomerId(): ?string
    {
        return $this->payments[0]['customerId'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getCustomerName(): ?string
    {
        return $this->payments[0]['customerName'] ?? null;
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
    public function getAuthorizedAmount(): Amount
    {
        return $this->authorizedAmount;
    }

    /**
     * @param Amount $authorizedAmount
     * @return $this
     */
    public function setAuthorizedAmount(Amount $authorizedAmount): self
    {
        $this->authorizedAmount = $authorizedAmount;
        return $this;
    }

    /**
     * @return Amount
     */
    public function getCapturedAmount(): Amount
    {
        return $this->capturedAmount;
    }

    /**
     * @param Amount $capturedAmount
     * @return $this
     */
    public function setCapturedAmount(Amount $capturedAmount): self
    {
        $this->capturedAmount = $capturedAmount;
        return $this;
    }

    /**
     * @return array
     */
    public function getCheckoutData(): array
    {
        return (array)$this->checkoutData;
    }

    /**
     * @param array $checkoutData
     * @return $this
     */
    public function setCheckoutData(array $checkoutData): self
    {
        $this->checkoutData = $checkoutData;
        return $this;
    }

    /**
     * @return array
     */
    public function getPayments(): array
    {
        return (array)$this->payments;
    }

    /**
     * @param array $payments
     * @return $this
     */
    public function setPayments(array $payments): self
    {
        $this->payments = $payments;
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

    /**
     * @return string
     */
    public function getModifiedAt(): string
    {
        return (string)$this->modifiedAt;
    }

    /**
     * @param string $modifiedAt
     * @return $this
     */
    public function setModifiedAt(string $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getModifiedBy(): string
    {
        return (string)$this->modifiedBy;
    }

    /**
     * @param string $modifiedBy
     * @return $this
     */
    public function setModifiedBy(string $modifiedBy): self
    {
        $this->modifiedBy = $modifiedBy;
        return $this;
    }

    /**
     * @return string
     */
    public function getExpiresAt(): string
    {
        return (string)$this->expiresAt;
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
    public function getCompletedAt(): string
    {
        return (string)$this->completedAt;
    }

    /**
     * @param string $completedAt
     * @return $this
     */
    public function setCompletedAt(string $completedAt): self
    {
        $this->completedAt = $completedAt;
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
     * @param array $links
     * @return $this
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;
        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getPaymentUrl()
    {
        return $this->links['redirect'] ?? '';
    }

    /**
     * @return mixed|string
     */
    public function getStatusUrl()
    {
        return $this->links['status'] ?? '';
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function isPaid()
    {
        return (new PayStatus())->get($this->getStatusCode()) === PayStatus::PAID;
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function isPending()
    {
        return (new PayStatus())->get($this->getStatusCode()) === PayStatus::PENDING;
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function isCancelled()
    {
        return (new PayStatus())->get($this->getStatusCode()) === PayStatus::CANCEL;
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function isPartialPayment()
    {
        return (new PayStatus())->get($this->getStatusCode()) === PayStatus::PARTIAL_PAYMENT;
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function isAuthorized(): bool
    {
        return (new PayStatus())->get($this->getStatusCode()) === PayStatus::AUTHORIZE;
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function isDenied(): bool
    {
        return (new PayStatus())->get($this->getStatusCode()) === PayStatus::DENIED;
    }
    
    /**
     * @return boolean
     * @throws Exception
     */
    public function isRefundedFully(): bool
    {
        return (new PayStatus())->get($this->getStatusCode()) === PayStatus::REFUND;
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function isRefundedPartial(): bool
    {
        return (new PayStatus())->get($this->getStatusCode()) === PayStatus::PARTIAL_REFUND;
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function isVoided(): bool
    {
        return (new PayStatus())->get($this->getStatusCode()) === PayStatus::VOID;
    }

    /**
     * @return boolean
     * @throws Exception
     */
    public function isBeingVerified()
    {
        return (new PayStatus())->get($this->getStatusCode()) === PayStatus::VERIFY;
    }

    /**
     * Check whether the status of the transaction is chargeback
     *
     * @return boolean
     */
    public function isChargeBack(): bool
    {
        return ($this->status['action'] ?? '') === 'CHARGEBACK';
    }

    /**
     * @param boolean $allowPartialRefunds
     * @return boolean
     * @throws Exception
     */
    public function isRefunded(bool $allowPartialRefunds = true): bool
    {
        if ($this->isRefundedFully()) {
            return true;
        }

        if ($allowPartialRefunds && $this->isRefundedPartial()) {
            return true;
        }

        return false;
    }
}
