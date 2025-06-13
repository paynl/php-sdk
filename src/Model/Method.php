<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

/**
 * Class Method
 *
 * @package PayNL\Sdk\Model
 */
class Method implements ModelInterface
{
    public const IDEAL = 10;
    public const PIN = 1927;
    public const PAYPAL = 138;
    public const RETOURPIN = 2351;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var array
     */
    protected $translations;

    /**
     * @var string
     */
    protected $image;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var int
     */
    protected $minAmount;

    /**
     * @var int
     */
    protected $maxAmount;

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string|null $language
     * @return string
     */
    public function getName(?string $language = null): string
    {
        if (!empty($language) && isset($this->translations['name'][$language])) {
            return $this->translations['name'][$language];
        }
        return $this->name;
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
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings ?? [];
    }

    /**
     * @param array $settings
     * @return $this
     */
    public function setSettings(array $settings): self
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasOptions(): bool
    {
        return !empty($this->options);
    }

    /**
     * @param string|null $language
     * @return string
     */
    public function getDescription(?string $language = null): string
    {
        if (!empty($language)) {
            if (isset($this->translations['description'][$language])) {
                return (string)$this->translations['description'][$language];
            }
            if (isset($this->translations['description'])) {
                foreach ($this->translations['description'] as $k => $v) {
                    if ($k == $v) {
                        if ($language == substr($k, 0, strlen($language))) {
                            return (string)$v;
                        }
                    }
                }
            }
        }

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
     * @return integer
     */
    public function getMinAmount(): int
    {
        return $this->minAmount;
    }

    /**
     * @param integer $minAmount
     * @return $this
     */
    public function setMinAmount(int $minAmount): self
    {
        $this->minAmount = $minAmount;
        return $this;
    }

    /**
     * @return integer
     */
    public function getMaxAmount(): int
    {
        return $this->maxAmount;
    }

    /**
     * @param integer $maxAmount
     * @return $this
     */
    public function setMaxAmount(int $maxAmount): self
    {
        $this->maxAmount = $maxAmount;
        return $this;
    }
}
