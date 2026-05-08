<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator\Filter;

interface FilterProviderInterface
{
    /**
     * Provides a filter for hydration
     */
    public function getFilter(): FilterInterface;
}
