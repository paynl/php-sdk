<?php

declare(strict_types=1);

namespace PayNL\Sdk\Exception;

use Exception;

/**
 * Class PayException
 */
class PayException extends Exception
{
    /**
     * @var string
     */
    private string $fMessage = 'Something went wrong';
    private int $payCode = 0;

    /**
     * @param $message
     * @param $payCode
     * @param $httpStatusCode
     */
    public function __construct($message, $payCode, $httpStatusCode)
    {
        $this->payCode = $payCode;
        parent::__construct($message, $httpStatusCode);
    }

    /**
     * @return string
     */
    public function getPayCode()
    {
        return 'PAY-' . $this->payCode;
    }

    /**
     * Returns customer friendly message
     *
     * @return string
     */
    public function getFriendlyMessage()
    {
        return $this->fMessage;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setFriendlyMessage(string $message): self
    {
        if (!empty($message)) {
            $this->fMessage = $message;
        }
        return $this;
    }

}
