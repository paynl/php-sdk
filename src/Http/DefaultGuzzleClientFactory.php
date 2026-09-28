<?php

declare(strict_types=1);

namespace PayNL\Sdk\Http;

use GuzzleHttp\Client;

final class DefaultGuzzleClientFactory implements GuzzleClientFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(array $config): Client
    {
        return new Client($config);
    }
}
