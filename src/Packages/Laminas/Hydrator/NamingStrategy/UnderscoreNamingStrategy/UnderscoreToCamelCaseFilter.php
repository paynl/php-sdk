<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

use function array_key_exists;
use function mb_strtolower;
use function mb_strtoupper;
use function preg_quote;
use function preg_replace_callback;
use function strtoupper;
use function substr;

/**
 * @internal
 */
final class UnderscoreToCamelCaseFilter
{
    use StringSupportTrait;

    /** @var string[] $transformedFilters */
    private $transformedFilters = [];

    public function filter(string $value): string
    {
        if (array_key_exists($value, $this->transformedFilters)) {
            return $this->transformedFilters[$value];
        }

        $pcreInfo = $this->getPatternAndReplacement(
            // a unicode safe way of converting characters to \x00\x00 notation
            preg_quote('_', '#')
        );

        $filtered = preg_replace_callback(
            $pcreInfo->pattern,
            $pcreInfo->replacement,
            $value
        );

        $lcFirstFunction = $this->getLcFirstFunction();
        /** @var string $filteredValue */
        $filteredValue = $lcFirstFunction($filtered);

        $this->transformedFilters[$value] = $filteredValue;

        return $filteredValue;
    }

    private function getPatternAndReplacement(string $pregQuotedSeparator): PcreReplacement
    {
        return $this->hasPcreUnicodeSupport()
            ? $this->getUnicodePatternAndReplacement($pregQuotedSeparator)
            : new PcreReplacement(
                '#(' . $pregQuotedSeparator . ')([\S]{1})#',
                function ($matches) {
                    return strtoupper($matches[2]);
                }
            );
    }

    private function getUnicodePatternAndReplacement(string $pregQuotedSeparator): PcreReplacement
    {
        return $this->hasMbStringSupport()
            ? new PcreReplacement(
                '#(' . $pregQuotedSeparator . ')(\P{Z}{1})#u',
                function ($matches) {
                    return mb_strtoupper($matches[2], 'UTF-8');
                }
            )
            : new PcreReplacement(
                '#(' . $pregQuotedSeparator . ')'
                    . '([^\p{Z}\p{Ll}]{1}|[a-zA-Z]{1})#u',
                function ($matches) {
                    return strtoupper($matches[2]);
                }
            );
    }

    private function getLcFirstFunction(): callable
    {
        return $this->hasMbStringSupport()
            ? function ($value) {
                return mb_strtolower($value[0], 'UTF-8') . substr($value, 1);
            }
            : 'lcfirst';
    }
}
