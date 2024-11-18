<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator;

trait HydratorAwareTrait
{
    /**
     * Hydrator instance
     *
     * @var null|HydratorInterface
     */
    protected $hydrator;

    /**
     * Set hydrator
     */
    public function setHydrator( $hydrator): void
    {
        $this->hydrator = $hydrator;
    }

    /**
     * Retrieve hydrator
     */
    public function getHydrator(): ?HydratorInterface
    {
        return $this->hydrator;
    }
}
