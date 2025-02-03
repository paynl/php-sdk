<?php

declare(strict_types=1);

if (false === function_exists('dbg')) {
    /**
     * @param string $message
     * @return void
     */
    function dbg(string $message): void
    {
        if (function_exists('displayPayDebug')) {
            displayPayDebug($message);
        }
    }
}
