<?php

namespace PayNL\Sdk\Util;

class ExchangeResponse
{
    private bool $result;
    private string $message;

    /**
     * @param bool $result
     * @param string $message
     */
    public function __construct(bool $result, string $message)
    {
        $this->result = $result;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function getResult(): bool
    {
        return $this->result;

    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
