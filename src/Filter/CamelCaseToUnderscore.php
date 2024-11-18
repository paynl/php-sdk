<?php

declare(strict_types=1);

namespace PayNL\Sdk\Filter;

class CamelCaseToUnderscore
{
    protected $separator = '_';

    /**
     * @param $value
     * @return array|mixed|string|string[]|null
     */
    public function filter($value)
    {
        if (! is_scalar($value) && ! is_array($value)) {
            return $value;
        }

        $pattern = ['#(?<=(?:\p{Lu}))(\p{Lu}\p{Ll})#', '#(?<=(?:\p{Ll}|\p{Nd}))(\p{Lu})#'];
        $replacement = [$this->separator . '\1', $this->separator . '\1'];

        return preg_replace($pattern, $replacement, $value);
    }
}
