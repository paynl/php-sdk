<?php

declare(strict_types=1);

namespace PayNL\Sdk\Hydrator;

trait HydratorAwareTrait
{
    protected ?AbstractHydrator $hydrator = null;

    public function getHydrator(): ?AbstractHydrator
    {
        return $this->hydrator;
    }

    public function setHydrator(AbstractHydrator $hydrator): self
    {
        $this->hydrator = $hydrator;
        return $this;
    }
}
