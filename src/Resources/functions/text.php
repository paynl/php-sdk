<?php

declare(strict_types=1);

use PayNL\Sdk\Util\Text;

if (false === function_exists('dbg')) {
    /**
     * @param string $message
     * @return string
     */
    function dbg(string $message): void
    {
        if (function_exists('displayPayDebug')) {
            displayPayDebug($message);
        }
    }
}
