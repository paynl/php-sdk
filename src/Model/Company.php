<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use JsonSerializable;
use PayNL\Sdk\Common\JsonSerializeTrait;

/**
 * Class Company
 *
 * @package PayNL\Sdk\Model
 */
class Company implements ModelInterface, JsonSerializable
{
    use JsonSerializeTrait;

    protected string $name = '';
    protected string $coc = '';
    protected string $vat = '';
    protected string $countryCode = '';

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->name;
    }

    /**
     * @param string $name
     *
     * @return Company
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCoc(): string
    {
        return (string)$this->coc;
    }

    /**
     * @param string $coc
     *
     * @return Company
     */
    public function setCoc(string $coc): self
    {
        $this->coc = $coc;
        return $this;
    }

    /**
     * @return string
     */
    public function getVat(): string
    {
        return (string)$this->vat;
    }

    /**
     * @param string $vat
     *
     * @return Company
     */
    public function setVat(string $vat): self
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return (string)$this->countryCode;
    }

    /**
     * @param string $countryCode
     *
     * @return Company
     */
    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;
        return $this;
    }
}
