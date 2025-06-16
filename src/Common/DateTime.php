<?php

declare(strict_types=1);

namespace PayNL\Sdk\Common;

use Exception,
    DateTime as stdDateTime,
    DateTimeZone,
    JsonSerializable
;

/**
 * Class DateTime
 *
 * Extends the PHP DateTime object to make it json serializable
 *
 * @package PayNL\Sdk
 */
class DateTime extends stdDateTime implements JsonSerializable
{
    /**
     * @param string $format
     * @param string $time
     * @param DateTimeZone|null $timezone
     * @return DateTime|false
     */
    public static function createFromFormat(string $format, string $time, ?DateTimeZone $timezone = null): DateTime|false
    {
        /** @var stdDateTime|false $dateTime */
        $dateTime = parent::createFromFormat($format, $time, $timezone);
        if ($dateTime !== false) {
            return (new self())->setTimestamp($dateTime->getTimestamp());
        }
        return false;
    }


    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return (string)$this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->format(static::ATOM);
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public static function now(): self
    {
        return new self();
    }
}
