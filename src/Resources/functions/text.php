<?php

declare(strict_types=1);

if (false === function_exists('paydbg')) {
    /**
     * @param string $message
     * @return void
     */
    function paydbg(string $message): void
    {
        if (function_exists('displayPayDebug')) {
            displayPayDebug($message);
        }
    }
}
