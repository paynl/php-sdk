<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use PayNL\Sdk\Common\AbstractTotalCollection;

/**
 * Class CheckoutOptions
 *
 * @package PayNL\Sdk\Model
 */
class CheckoutOptions extends AbstractTotalCollection implements ModelInterface
{
    /**
     * @return array
     */
    public function getCheckoutOptions(): array
    {
        return $this->toArray();
    }

    /**
     * @param array $checkoutoptions
     *
     * @return CheckoutOptions
     */
    public function setCheckoutOptions(array $checkoutoptions): self
    {
        $this->clear();

        if (0 === count($checkoutoptions)) {
            return $this;
        }

        foreach ($checkoutoptions as $checkoutoption) {
            $this->addCheckoutOption($checkoutoption);
        }

        return $this;
    }

    /**
     * @param CheckoutOption $checkoutoption
     *
     * @return CheckoutOptions
     */
    public function addCheckoutOption(CheckoutOption $checkoutoption): self
    {
        $this->set($checkoutoption->getTag(), $checkoutoption);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCollectionName(): string
    {
        return 'checkoutoptions';
    }
}
