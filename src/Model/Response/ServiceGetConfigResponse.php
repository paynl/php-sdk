<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Response;

use PayNL\Sdk\Exception\InvalidArgumentException;
use PayNL\Sdk\Model\ModelInterface;
use PayNL\Sdk\Model\CheckoutOptions;
use PayNL\Sdk\Model\Method;

/**
 * Class ServiceGetConfigResponse
 *
 * @package PayNL\Sdk\Model
 */
class ServiceGetConfigResponse implements ModelInterface
{
    /**
     * @required
     *
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $secret = '';

    /**
     * @var bool
     */
    protected $testMode;

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
    protected $status = '';

    /**
     * @var array
     */
    protected $merchant;

    /**
     * @var array
     */
    protected $category;

    /**
     * @var array
     */
    protected $turnoverGroup;

    /**
     * @var array
     */
    protected $layout;

    /**
     * @var CheckoutOptions
     */
    protected $checkoutOptions;

    /**
     * @var array
     */
    protected $checkoutSequence;

    /**
     * @var array
     */
    protected $tguList;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return (string)$this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
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
     *
     * @return Config
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTestMode(): bool
    {
        return $this->testMode == 1;
    }

    /**
     * @param boolean $testMode
     *
     * @throws InvalidArgumentException when given test mode is invalid
     *
     * @return Config
     */
    public function setTestMode(bool $testMode): self
    {
        $this->testMode = $testMode;
        return $this;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return (string)$this->secret;
    }

    /**
     * @param string $secret
     *
     * @return Config
     */
    public function setSecret(string $secret): self
    {
        $this->secret = $secret;
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
     * @return void
     */
    public function setTranslations(array $translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (string)$this->status;
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getMerchant(): array
    {
        return $this->merchant;
    }

    /**
     * @param array $merchant
     * @return void
     */
    public function setMerchant(array $merchant): void
    {
        $this->merchant = $merchant;
    }

    /**
     * @return boolean
     */
    public function getTestMode(): bool
    {
        return $this->testMode;
    }

    /**
     * @return array
     */
    public function getCheckoutSequence(): array
    {
        return $this->checkoutSequence;
    }

    /**
     * @param array $checkoutSequence
     * @return void
     */
    public function setCheckoutSequence(array $checkoutSequence): void
    {
        $this->checkoutSequence = $checkoutSequence;
    }

    /**
     * @return CheckoutOptions
     */
    public function getCheckoutOptions(): CheckoutOptions
    {
        return $this->checkoutOptions;
    }

    /**
     * @return array
     */
    public function getPaymentMethods(): array
    {
        $methods = [];
        foreach ($this->getCheckoutOptions() as $checkoutOption) {
            $tag = $checkoutOption->getTag();
            if (str_starts_with($tag, 'PG')) {
                # Methods are "grouped" so only return the group "method"
                $groupMethod = new Method();
                $groupMethod->setId($checkoutOption->getId());
                $groupMethod->setName($checkoutOption->getName());
                $groupMethod->setDescription($checkoutOption->getName());
                $groupMethod->setTranslations($checkoutOption->getTranslations());
                $groupMethod->setImage($checkoutOption->getImage());
                $groupMethod->setMinAmount(0);
                $groupMethod->setMaxAmount(0);
                $methods[] = $groupMethod;
            } else {
                foreach ($checkoutOption->getPaymentMethods() as $method) {
                    $methods[] = $method;
                }
            }
        }
        return $methods;
    }

    /**
     * @param CheckoutOptions $checkoutOptions
     * @return void
     */
    public function setCheckoutOptions(CheckoutOptions $checkoutOptions): void
    {
        $this->checkoutOptions = $checkoutOptions;
    }

    /**
     * @return array
     */
    public function getLayout(): array
    {
        return $this->layout;
    }

    /**
     * @param array $layout
     * @return void
     */
    public function setLayout(array $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * @return array
     */
    public function getTurnoverGroup(): array
    {
        return $this->turnoverGroup;
    }

    /**
     * @param array $turnoverGroup
     * @return void
     */
    public function setTurnoverGroup(array $turnoverGroup): void
    {
        $this->turnoverGroup = $turnoverGroup;
    }

    /**
     * @return array
     */
    public function getCategory(): array
    {
        return $this->category;
    }

    /**
     * @param array $category
     * @return void
     */
    public function setCategory(array $category): void
    {
        $this->category = $category;
    }

    /**
     * @return array
     */
    public function getTguList(): array
    {
        return $this->tguList ?? [];
    }

    /**
     * @param array $tguList
     * @return void
     */
    public function setTguList(array $tguList): void
    {
        $this->tguList = $tguList;
    }

    /**
     * Provides a core-list prepared with web protocol.
     * @return array
     */
    public function getCores(): array
    {
        $cores = $this->getTguList();

        foreach ($cores as &$core) {
            $domain = $core['domain'];

            $parts = explode('.', $domain);
            if (count($parts) === 2) {
                $domain = 'connect.' . $domain;
            }

            $core['domain'] = 'https://' . $domain;
            $core['label'] = $domain;
        }

        # Sort on share
        usort($cores, function ($a, $b) {
            return $b['share'] <=> $a['share'];
        });

        return $cores;
    }

    /**
     * Returns banks if iDEAL is enabled
     * @return array
     */
    public function getBanks(): array
    {
        foreach ($this->getCheckoutOptions() as $checkoutOption) {
            foreach ($checkoutOption->getPaymentMethods() as $method) {
                if ($method->getId() == Method::IDEAL && $method->hasOptions()) {
                    return $method->getOptions();
                }
            }
        }
        return [];
    }

    /**
     * Returns terminals if Terminal Payments(PIN) is enabled
     * @return array
     */
    public function getTerminals(): array
    {
        foreach ($this->getCheckoutOptions() as $checkoutOption) {
            foreach ($checkoutOption->getPaymentMethods() as $method) {
                if ($method->getId() == Method::PIN && $method->hasOptions()) {
                    return $method->getOptions();
                }
            }
        }
        return [];
    }
}
