<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator\Filter;

use function strpos;
use function substr;

final class MethodMatchFilter implements FilterInterface
{
    /**
     * The method to exclude
     *
     * @var string
     */
    protected $method;

    /**
     * Either an exclude or an include
     *
     * @var bool
     */
    protected $exclude;

    /**
     * @param string $method The method to exclude or include
     * @param bool $exclude If the method should be excluded
     */
    public function __construct(string $method, bool $exclude = true)
    {
        $this->method  = $method;
        $this->exclude = $exclude;
    }

    public function filter(string $property, ?object $instance = null): bool
    {
        $pos = strpos($property, '::');
        if ($pos !== false) {
            $pos += 2;
        } else {
            $pos = 0;
        }

        return substr($property, $pos) === $this->method
            ? ! $this->exclude
            : $this->exclude;
    }
}
