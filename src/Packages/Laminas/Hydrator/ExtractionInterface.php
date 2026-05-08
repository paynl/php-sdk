<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator;

interface ExtractionInterface
{
    /**
     * Extract values from an object
     *
     * @return mixed[]
     */
    public function extract(object $object): array;
}
