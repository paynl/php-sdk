<?php

declare(strict_types=1);

namespace PayNL\Sdk\Util;

use PayNL\Sdk\{
    Exception\InvalidArgumentException,
    Exception\LogicException
};
use PHPUnit\Framework\Exception;

/**
 * Class Misc
 *
 * @package PayNL\Sdk\Util
 */
class Misc
{
    /**
     * @param string $file
     *
     * @return string
     * @throws LogicException when the class name is not the same as the terminating class file name
     *  (PSR-4 3.3 - https://www.php-fig.org/psr/psr-4/)
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @throws InvalidArgumentException when given file can not be found or read
     */
    public static function getClassNameByFile(string $file): string
    {
        try {
            /** @var resource $handle */
            $handle = fopen($file, 'rb');
        } catch (Exception $exception) {
            throw new InvalidArgumentException(
                sprintf(
                    'Class can not be found because file "%s" does not exist or can not be read',
                    $file
                )
            );
        }

        $class = $namespace = $buffer = '';
        $counter = 0;
        while (true === empty($class)) {
            if (true === feof($handle)) {
                break;
            }

            $buffer .= fread($handle, 512);
            $tokens = @token_get_all($buffer);

            if (strpos($buffer, '{') === false) {
                continue;
            }

            $tokenCount = count($tokens);

            for (; $counter < $tokenCount; $counter++) {
                if ($tokens[$counter][0] === T_NAMESPACE) {
                    $namespaceIdentifier = PHP_VERSION_ID < 80000 ? T_STRING : T_NAME_QUALIFIED;

                    for ($nextCounter = $counter + 1; $nextCounter < $tokenCount; $nextCounter++) {
                        if ($tokens[$nextCounter][0] == $namespaceIdentifier) {
                            $namespace .= '\\' . $tokens[$nextCounter][1];
                        } elseif ($tokens[$nextCounter] === '{' || $tokens[$nextCounter] === ';') {
                            break;
                        }
                    }
                }

                if ($tokens[$counter][0] === T_CLASS) {
                    for ($nextCounter = $counter + 1; $nextCounter < $tokenCount; $nextCounter++) {
                        if ($tokens[$nextCounter] === '{') {
                            $class = $tokens[$counter + 2][1];
                        }
                    }
                }
            }
        }

        $filename = substr(basename($file), 0, (int)strpos(basename($file), '.'));
        if ($filename !== $class) {
            throw new LogicException(
                sprintf(
                    'Class name "%s" is not the same as the terminating class file name "%s"',
                    $class,
                    $filename
                )
            );
        }

        return $namespace . '\\' . $class;
    }

    /**
     * @param string $fqn
     *
     * @return string
     */
    public static function getClassNameByFQN(string $fqn): string
    {
        $namespaceSeparator = '\\';
        $parts = explode($namespaceSeparator, $fqn);
        return array_pop($parts) ?? '';
    }

    /**
     * Get the IP of the user
     * @return string|false
     */
    public function getIp(): string|false
    {
        $headers = $_SERVER;
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        }

        // Get the forwarded IP if it exists
        $the_ip = $_SERVER['REMOTE_ADDR'];
        if (array_key_exists('X-Forwarded-For', $headers)) {
            $the_ip = $headers['X-Forwarded-For'];
        } elseif (array_key_exists('HTTP_X_FORWARDED_FOR', $headers)) {
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        }
        $arrIp = explode(',', $the_ip);

        return filter_var(trim(trim(!empty($arrIp[0]) ? $arrIp[0] : ''), '[]'), FILTER_VALIDATE_IP);
    }

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
     * Determine if a transaction ID is a TGU transaction.
     *
     * @param string|null $transactionId
     * @return bool
     */
    public static function isTguTransaction(?string $transactionId): bool
    {
        $pid = trim((string)$transactionId);
        $id = $pid[0] ?? null;

        return $id !== null && ctype_digit($id) && (int)$id > 3;
    }


}
