<?php

declare(strict_types=1);

namespace PayNL\Sdk\Common;

/**
 * Interface DebugAwareInterface
 *
 * @package PayNL\Sdk\Common
 */
interface DebugAwareInterface
{
    /**
     * @return boolean
     */
    public function isDebug(): bool;

    /**
     * @param boolean $debug
     *
     * @return static
     */
    public function setDebug(bool $debug);
}
