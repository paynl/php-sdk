<?php

declare (strict_types=1);

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
     * @param string $address
     *
     * @return array
     */
    public function splitAddress(string $address): array
    {
        $street = $number = '';

        $address = trim($address);
        $addressParts = preg_split('/(\s+)(\d+)/', $address, 2, PREG_SPLIT_DELIM_CAPTURE);

        if (true === is_array($addressParts)) {
            $street = trim(array_shift($addressParts) ?? '');
            $number = trim(implode('', $addressParts));
        }

        if (true === empty($street) || true === empty($number)) {
            $addressParts = preg_split('/([A-z]{2,})/', $address, 2, PREG_SPLIT_DELIM_CAPTURE);

            if (true === is_array($addressParts)) {
                $number = trim(array_shift($addressParts) ?? '');
                $street = trim(implode('', $addressParts));
            }
        }

        $number = substr($number, 0, 45);

        return compact('street', 'number');
    }

    /**
     * @param $errorMessage
     * @return string
     */
    public static function getFriendlyMessage($errorMessage)
    {
        $friendlyMessages = [
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
