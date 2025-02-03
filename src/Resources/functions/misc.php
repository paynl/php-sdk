<?php

declare(strict_types=1);

use PayNL\Sdk\Util\Misc;

if (false === function_exists('paynl_split_address')) {
    /**
     * @param string $address
     *
     * @return array
     */
    function paynl_split_address(string $address): array
    {
        return (new Misc())->splitAddress($address);
    }
}

if (false === function_exists('paynl_get_ip')) {
    /**
     * @return mixed
     */
    function paynl_get_ip(): mixed
    {
        return (new Misc())->getIp();
    }
}
