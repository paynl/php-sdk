<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use DateTime,
    JsonSerializable;
use PayNL\Sdk\{
    Exception\InvalidArgumentException,
    Common\JsonSerializeTrait
};

/**
 * Class Customer
 *
 * @package PayNL\Sdk\Model
 */
class Customer implements ModelInterface, JsonSerializable
{
    use JsonSerializeTrait;

    protected string $firstName = '';
    protected string $lastName = '';
    protected string $ipAddress = '';
    protected string $birthDate = '';
    protected string $gender = '';
    protected string $phone = '';
    protected string $email = '';
    protected string $language = '';
    protected int $trust = 0;
    protected string $reference = '';
    protected ?Company $company = null;
    protected string $locale = '';

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return (string)$this->lastName;
    }

    /**
     * @param string $locale
     * @return Customer
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $lastName
     *
     * @return Customer
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return (string)$this->ipAddress;
    }

    /**
     * @param string $ip
     *
     * @return Customer
     */
    public function setIpAddress(string $ip): self
    {
        $this->ipAddress = $ip;
        return $this;
    }

    /**
     * @return string
     */
    public function getBirthDate(): string
    {
        return (string)$this->birthDate;
    }

    /**
     * @param string|null $birthDate
     * @return $this
     */
    public function setBirthDate(?string $birthDate): self
    {
        if ($birthDate === null || trim($birthDate) === '') {
            return $this;
        }

        $birthDate = trim($birthDate);
        $dateFormats = ['Y-m-d', 'd-m-Y', \DateTime::ATOM];

        foreach ($dateFormats as $format) {
            $dt = \DateTime::createFromFormat($format, $birthDate);
            if ($dt && $dt->format($format) === $birthDate) {
                $this->birthDate = $dt->format('Y-m-d');
                return $this;
            }
        }

        paydbg("Skipping invalid birthDate format: $birthDate");
        return $this;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return (string)$this->gender;
    }

    /**
     * @param string $gender
     *
     * @return Customer
     */
    public function setGender(string $gender): self
    {
        $this->gender = strtoupper($gender);
        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return (string)$this->phone;
    }

    /**
     * @param string $phone
     *
     * @return Customer
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return (string)$this->email;
    }

    /**
     * @param string $email
     *
     * @return Customer
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return int
     */
    public function getTrust(): int
    {
        return $this->trust;
    }

    /**
     * @param string $trust
     *
     * @throws InvalidArgumentException
     *
     * @return Customer
     */
    public function setTrust(string $trustLevel): self
    {
        $min = -10;
        $max = 10;
        $trustLevel = (int) $trustLevel;
        if (false === in_array($trustLevel, range($min, $max), true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Given trust level "%d" to %s is not valid, choose one between %d and %d',
                    $trustLevel,
                    __METHOD__,
                    $min,
                    $max
                )
            );
        }

        $this->trust = $trustLevel;
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
     *
     * @return Customer
     */
    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return Company
     */
    public function getCompany(): Company
    {
        if (null === $this->company) {
            $this->setCompany(new Company());
        }
        return $this->company;
    }

    /**
     * @param Company $company
     *
     * @return Customer
     */
    public function setCompany(Company $company): self
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Customer
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return Customer
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }
}
