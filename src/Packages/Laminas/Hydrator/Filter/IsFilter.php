<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator\Filter;

use function strpos;

final class IsFilter implements FilterInterface
{
    public function filter(string $property, ?object $instance = null): bool
    {
        $pos = strpos($property, '::');
        if ($pos !== false) {
            $pos += 2;
        } else {
            $pos = 0;
        }

        return strpos($property, 'is', $pos) === $pos;
    }
}
