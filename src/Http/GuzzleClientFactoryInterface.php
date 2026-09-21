<?php

declare(strict_types=1);

namespace PayNL\Sdk\Http;

use GuzzleHttp\Client;

interface GuzzleClientFactoryInterface
{
    /**
     * Create a client using the SDK's endpoint configuration.
     *
     * @param array $config
     * @return Client
     */
    public function create(array $config): Client;
}
