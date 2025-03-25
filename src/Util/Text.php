<?php

declare(strict_types=1);

namespace PayNL\Sdk\Util;

use PayNL\Sdk\Config\Config;

/**
 * Class Text
 *
 * @package PayNL\Sdk\Util
 */
class Text
{
    /**
     * @param string $errorMessage
     * @return string
     */
    public static function getFriendlyMessage(string $errorMessage)
    {
        $friendlyMessages = [
          'requested payment method is not enabled' => 'This payment method is not available',
          'INVALID_TRANSACTION_STAT' => 'Transaction not ready for refund.',
          'username can not be empty' => 'Connection error. Please check your connection credentials.',
          'bestelling kon niet worden gevonden' => 'Your order could not be found',
          'Transaction cannot be aborted in this state' => 'Unfortunately the transaction cannot be aborted',
          'not enabled for this service' => 'Unfortunately this payment method is not available',
          'Minimum amount for this payment method' => 'Unfortunately the order amount is too low for this payment method',
          'exceeded for payment option' => 'Unfortunately the order amount is too high for this payment method',
          'Value is not a valid regionCode' => 'Unfortunately the entered regionCode is not a correct regionCode',
          'terminal not connected' => 'The selected terminal is not connected',
          'Forbidden' => ['Wrong credentials. Please check your SDK configuration.']
        ];
        foreach ($friendlyMessages as $needle => $newMessage) {
            if (is_array($newMessage)) {
                if ($errorMessage == $needle) {
                    return $newMessage[0];
                }
            } else {
                if (stripos($errorMessage, $needle) !== false) {
                    return $newMessage;
                }
            }
        }
        return '';
    }
}
