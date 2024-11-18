<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

/**
 * Describe a PCRE pattern and a callback for providing a replacement.
 *
 * @internal
 */
class PcreReplacement
{
    /** @var string */
    public $pattern;

    /** @var callable */
    public $replacement;

    public function __construct(string $pattern, callable $replacement)
    {
        $this->pattern     = $pattern;
        $this->replacement = $replacement;
    }
}
