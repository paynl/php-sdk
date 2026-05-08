<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator;

interface HydratorAwareInterface
{
    /**
     * Set hydrator
     */
    public function setHydrator(HydratorInterface $hydrator): void;

    /**
     * Retrieve hydrator
     */
    public function getHydrator(): ?HydratorInterface;
}
