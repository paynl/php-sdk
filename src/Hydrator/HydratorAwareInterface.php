<?php

declare(strict_types=1);

namespace PayNL\Sdk\Hydrator;

interface HydratorAwareInterface
{
    /**
     * Get the configured hydrator.
     */
    public function getHydrator(): ?AbstractHydrator;

    /**
     * Set the hydrator.
     */
    public function setHydrator(AbstractHydrator $hydrator);
}
