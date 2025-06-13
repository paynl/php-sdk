<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use JsonSerializable;
use PayNL\Sdk\Common\JsonSerializeTrait;

/**
 * Class Amount
 *
 * @package PayNL\Sdk\Model
 */
class Amount implements ModelInterface, JsonSerializable
{
    use JsonSerializeTrait;

    /**
     * @var int
     */
    protected $value = 0;

    /**
     * @required
     *
     * @var string
     */
    protected $currency = 'EUR';

    /**
     * @param int|null $value
     * @param string|null $currency
     */
    public function __construct(?int $value = null, ?string $currency = null)
    {
        if (!is_null($value)) {
            $this->setValue($value);
        }
        if (!is_null($currency)) {
            $this->setCurrency($currency);
        }
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return Amount
     */
    public function setCurrency(string $currency): Amount
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setValue(int $value): Amount
    {
        $this->value = $value;
        return $this;
    }
}
