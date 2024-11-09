<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

use PayNL\Sdk\Packages\Laminas\Stdlib\StringUtils;

use function extension_loaded;

/**
 * @internal
 */
trait StringSupportTrait
{
    /** @var bool */
    private $pcreUnicodeSupport;

    /** @var bool */
    private $mbStringSupport;

    private function hasPcreUnicodeSupport(): bool
    {
        if ($this->pcreUnicodeSupport === null) {
            $this->pcreUnicodeSupport = StringUtils::hasPcreUnicodeSupport();
        }
        return $this->pcreUnicodeSupport;
    }

    private function hasMbStringSupport(): bool
    {
        if ($this->mbStringSupport === null) {
            $this->mbStringSupport = extension_loaded('mbstring');
        }
        return $this->mbStringSupport;
    }
}
