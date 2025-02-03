<?php

namespace PayNL\Sdk\Util;

class ExchangeResponse
{
    private bool $result;
    private string $message;

    /**
     * @param boolean $result
     * @param string $message
     */
    public function __construct(bool $result, string $message)
    {
        $this->result = $result;
        $this->message = $message;
    }

    /**
     * @return boolean
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
