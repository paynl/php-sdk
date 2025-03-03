<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Stdlib;

use ArrayAccess;
use Countable;
use Serializable;
use Traversable;

/**
 * Basically, an ArrayObject. You could simply define something like:
 *     class QueryParams extends ArrayObject implements Parameters {}
 * and have 90% of the functionality
 *
 * @template TKey
 * @template TValue
 * @template-extends ArrayAccess<TKey, TValue>
 * @template-extends Traversable<TKey, TValue>
 */
interface ParametersInterface extends ArrayAccess, Countable, Serializable, Traversable
{
    /**
     * Constructor
     *
     * @param array<TKey, TValue>|null $values
     */
    public function __construct(?array $values = null);

    /**
     * From array
     *
     * Allow deserialization from standard array
     *
     * @param array<TKey, TValue> $values
     * @return mixed
     */
    public function fromArray(array $values);

    /**
     * From string
     *
     * Allow deserialization from raw body; e.g., for PUT requests
     *
     * @param string $string
     * @return mixed
     */
    public function fromString($string);

    /**
     * To array
     *
     * Allow serialization back to standard array
     *
     * @return array<TKey, TValue>
     */
    public function toArray();

    /**
     * To string
     *
     * Allow serialization to query format; e.g., for PUT or POST requests
     *
     * @return string
     */
    public function toString();

    /**
     * @param TKey $name
     * @param TValue|null $default
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * @param TKey $name
     * @param TValue $value
     * @return ParametersInterface
     */
    public function set($name, $value);
}
