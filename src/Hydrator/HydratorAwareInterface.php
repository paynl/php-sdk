<?php

declare(strict_types=1);

namespace PayNL\Sdk\Hydrator;

interface HydratorAwareInterface
{
    public function getHydrator(): ?AbstractHydrator;

    public function setHydrator(AbstractHydrator $hydrator);
}
