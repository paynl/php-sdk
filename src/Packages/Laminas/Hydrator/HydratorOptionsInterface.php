<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator;

interface HydratorOptionsInterface
{
    /**
     * @param mixed[] $options
     */
    public function setOptions(iterable $options): void;
}
