<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model;

use PayNL\Sdk\Common\AbstractTotalCollection;

/**
 * Class Methods
 *
 * @package PayNL\Sdk\Model
 */
class Methods extends AbstractTotalCollection implements ModelInterface
{
    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->toArray();
    }

    /**
     * @param array $methods
     *
     * @return Methods
     */
    public function setMethods(array $methods): self
    {
        $this->clear();

        if (0 === count($methods)) {
            return $this;
        }

        foreach ($methods as $method) {
            $this->addMethod($method);
        }

        return $this;
    }

    /**
     * @param Method $method
     *
     * @return Methods
     */
    public function addMethod(Method $method): self
    {
        $this->set($method->getId(), $method);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCollectionName(): string
    {
        return 'methods';
    }
}
