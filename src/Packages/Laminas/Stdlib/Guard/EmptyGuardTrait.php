<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Stdlib\Guard;

use Exception;
use PayNL\Sdk\Packages\Laminas\Stdlib\Exception\InvalidArgumentException;

use function sprintf;

/**
 * Provide a guard method against empty data
 */
trait EmptyGuardTrait
{
    /**
     * Verify that the data is not empty
     *
     * @param mixed  $data           the data to verify
     * @param string $dataName       the data name
     * @param string $exceptionClass FQCN for the exception
     * @return void
     * @throws Exception
     */
    protected function guardAgainstEmpty(
        mixed $data,
        $dataName = 'Argument',
        $exceptionClass = InvalidArgumentException::class
    ) {
        if (empty($data)) {
            $message = sprintf('%s cannot be empty', $dataName);
            throw new $exceptionClass($message);
        }
    }
}
