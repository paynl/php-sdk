<?php

declare(strict_types=1);

namespace PayNL\Sdk\Request;

use PayNL\Sdk\Exception\BadMethodCallException;

/**
 * Class Request
 *
 * @package PayNL\Sdk\Request
 */
class Request extends AbstractRequest
{
    /**
     * Request constructor.
     *
     * @param string $uri
     * @param string $method
     * @param array $requiredParams
     * @param array $options
     *
     * @throws BadMethodCallException
     */
    public function __construct(
        string $uri,
        string $method = self::METHOD_GET,
        array $requiredParams = [],
        array $options = [],
        array $optionalParams = []
    ) {
        $this->setUri($uri)
             ->setMethod($method)
             ->setRequiredParams($requiredParams)
             ->setOptionalParams($optionalParams);

        parent::__construct($options);
    }
}
