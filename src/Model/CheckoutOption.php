<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

/**
 * Class CheckoutOption
 *
 * @package PayNL\Sdk\Model
 */
class CheckoutOption implements ModelInterface
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $tag;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $translations;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var Methods
     */
    protected $paymentMethods;

    /**
     * @var array
     */
    protected $requiredFields;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return (string)$this->tag;
    }

    /**
     * @param string $tag
     * @return $this
     */
    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    /**
     * @param array $translations
     * @return $this
     */
    public function setTranslations(array $translations): self
    {
        $this->translations = $translations;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return (string)$this->image;
    }

    /**
     * @param string $image
     * @return $this
     */
    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return Methods
     */
    public function getPaymentMethods(): Methods
    {
        return $this->paymentMethods;
    }

    /**
     * @param Methods $paymentMethods
     * @return $this
     */
    public function setPaymentMethods(Methods $paymentMethods): self
    {
        $this->paymentMethods = $paymentMethods;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        return (array)$this->requiredFields;
    }

    /**
     * @param array $requiredFields
     * @return $this
     */
    public function setRequiredFields(array $requiredFields): self
    {
        $this->requiredFields = $requiredFields;
        return $this;
    }

}
