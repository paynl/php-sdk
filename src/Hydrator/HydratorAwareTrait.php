<?php

declare(strict_types=1);

namespace PayNL\Sdk\Hydrator;

trait HydratorAwareTrait
{
    protected ?AbstractHydrator $hydrator = null;

    /**
     * Get the configured hydrator.
     */
    public function getHydrator(): ?AbstractHydrator
    {
        return $this->hydrator;
    }

    /**
     * Set the hydrator.
     */
    public function setHydrator(AbstractHydrator $hydrator): self
    {
        $this->hydrator = $hydrator;
        return $this;
    }
}
