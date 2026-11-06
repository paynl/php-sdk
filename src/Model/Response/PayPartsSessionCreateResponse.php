<?php

declare(strict_types=1);

namespace PayNL\Sdk\Model\Response;

use PayNL\Sdk\Model\ModelInterface;

/**
 * ID and token used to initialize the PayParts JavaScript SDK.
 */
class PayPartsSessionCreateResponse implements ModelInterface
{
    /**
     * @var string
     */
    protected string $sessionId = '';

    /**
     * Set the sessionId returned by the API.
     */
    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * Get the sessionId returned by the API.
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @var string
     */
    protected string $sessionToken = '';

    /**
     * Set the sessionToken returned by the API.
     */
    public function setSessionToken(string $sessionToken): self
    {
        $this->sessionToken = $sessionToken;
        return $this;
    }

    /**
     * Get the sessionToken returned by the API.
     */
    public function getSessionToken(): string
    {
        return $this->sessionToken;
    }
}
