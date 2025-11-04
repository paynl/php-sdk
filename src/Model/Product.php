<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use JsonSerializable;
use PayNL\Sdk\Common\JsonSerializeTrait;

/**
 * Class Product
 *
 * @package PayNL\Sdk\Model
 */
class Product implements ModelInterface, JsonSerializable
{
    const TYPE_ARTICLE = 'article';
    const TYPE_SHIPPING = 'shipping';
    const TYPE_ROUNDING = 'rounding';
    const TYPE_HANDLING = 'handling';
    const TYPE_PAYMENT = 'payment';
    const TYPE_CREDIT = 'credit';
    const TYPE_GIFTCARD= 'giftcard';
    const TYPE_EMONEY = 'emoney';
    const TYPE_CRYPTO = 'crypto';
    const TYPE_DISCOUNT = 'discount';

    const VAT_N = 'N';
    const VAT_HIGH = 'H';
    const VAT_LOW = 'L';

    use JsonSerializeTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Amount
     */
    protected $price;

    /**
     * @var float
     */
    protected float $quantity;

    /**
     * @var string
     */
    protected ?string $vatCode = null;

    /**
     * @var float|null
     */
    protected ?float $vatPercentage = null;

    /**
     * @param $id
     * @param $description
     * @param $amount
     * @param $currency
     * @param $type
     * @param $quantity
     * @param string|null $vatCode
     * @param float|null $vatPercentage
     */
    public function __construct(
        $id = null,
        $description = null,
        $amount = null,
        $currency = null,
        $type = null,
        $quantity = null,
        ?string $vatCode = null,
        ?float $vatPercentage = null
    )
    {
        if (!is_null($id)) {
            $this->setId($id);
        }
        if (!is_null($description)) {
            $this->setDescription($description);
        }
        if (!is_null($amount)) {
            $this->setAmount($amount);
        }
        if (!is_null($currency)) {
            $this->setCurrency($currency);
        }
        if (!is_null($type)) {
            $this->setType($type);
        }
        if (!is_null($quantity)) {
            $this->setQuantity((float)$quantity);
        }
        if (!is_null($vatCode)) {
            $this->setVatCode($vatCode);
        }
        if (!is_null($vatPercentage)) {
            $this->setVatPercentage($vatPercentage);
        }
    }

    /*
     * Set amount for product in full price (not in cents)
     */
    public function setAmount($amount)
    {
        $this->setPrice($this->getPrice()->setValue((int)round($amount * 100)));
    }

    /*
     * Set currency for product
     */
    public function setCurrency($currency)
    {
        $this->setPrice($this->getPrice()->setCurrency($currency));
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
     *
     * @return Product
     */
    public function setId(string $id): self
    {
        $this->id = $id;
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
     *
     * @return Product
     */
    public function setType(string $type): Product
    {
        $this->type = $type;
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
        $description = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $description);
        $this->description = $description;
        return $this;
    }

    /**
     * @return Amount
     */
    public function getPrice(): Amount
    {
        if (null === $this->price) {
            $this->setPrice(new Amount());
        }
        return $this->price;
    }

    /**
     * @param Amount $price
     *
     * @return Product
     */
    public function setPrice(Amount $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity(): float
    {
        return (float)$this->quantity;
    }

    /**
     * @param float $quantity
     *
     * @return Product
     */
    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getVatCode(): string
    {
        return (string)$this->vatCode;
    }

    /**
     * @param string $vatCode
     * @return $this
     */
    public function setVatCode(string $vatCode): self
    {
        $this->vatCode = $vatCode;
        return $this;
    }

    /**
     * @return null|float
     */
    public function getVatPercentage(): ?float
    {
        return $this->vatPercentage;
    }

    /**
     * @param float $vatPercentage
     * @return $this
     */
    public function setVatPercentage(float $vatPercentage): self
    {
        $this->vatPercentage = $vatPercentage;
        return $this;
    }
}