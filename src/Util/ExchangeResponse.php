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
        $this->setResult($result);
        $this->setMessage($message);
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

    /**
     * @param bool $result
     * @return self
     */
    public function setResult(bool $result): self
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param bool $result
     * @param string $message
     * @return $this
     */
    public function set(bool $result, string $message): self
    {
        $this->setResult($result);
        $this->setMessage($message);
        return $this;
    }

}
