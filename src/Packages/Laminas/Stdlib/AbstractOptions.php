<?php

declare(strict_types=1);

namespace PayNL\Sdk\Packages\Laminas\Stdlib;

use Traversable;

use function array_shift;
use function is_array;
use function is_callable;
use function method_exists;
use function preg_replace_callback;
use function sprintf;
use function str_replace;
use function strtolower;
use function ucwords;

abstract class AbstractOptions implements ParameterObjectInterface
{
    // phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore,WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCapsProperty

    /**
     * We use the __ prefix to avoid collisions with properties in
     * user-implementations.
     *
     * @var bool
     */
    protected $__strictMode__ = true;

    // phpcs:enable

    /**
     * Constructor
     *
     * @param  array|Traversable|null $options
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setFromArray($options);
        }
    }

    /**
     * Set one or more configuration properties
     *
     * @param  array|Traversable|AbstractOptions $options
     * @throws Exception\InvalidArgumentException
     * @return AbstractOptions Provides fluent interface
     */
    public function setFromArray($options)
    {
        if ($options instanceof self) {
            $options = $options->toArray();
        }

        if (! is_array($options) && ! $options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Parameter provided to %s must be an %s, %s or %s',
                    __METHOD__,
                    'array',
                    'Traversable',
                    self::class
                )
            );
        }

        foreach ($options as $key => $value) {
            $this->__set($key, $value);
        }

        return $this;
    }

    /**
     * Cast to array
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];

        /** @param string[] $letters */
        $transform = static function (array $letters): string {
            $letter = array_shift($letters);
            return '_' . strtolower($letter);
        };

        foreach ($this as $key => $value) {
            if ($key === '__strictMode__') {
                continue;
            }
            $normalizedKey         = preg_replace_callback('/([A-Z])/', $transform, $key);
            $array[$normalizedKey] = $value;
        }

        return $array;
    }

    /**
     * Set a configuration property
     *
     * @see ParameterObject::__set()
     *
     * @param string $key
     * @param mixed $value
     * @throws Exception\BadMethodCallException
     * @return void
     */
    public function __set($key, $value)
    {
        $setter = 'set' . str_replace('_', '', $key);

        if (is_callable([$this, $setter])) {
            $this->{$setter}($value);

            return;
        }

        if ($this->__strictMode__) {
            throw new Exception\BadMethodCallException(sprintf(
                'The option "%s" does not have a callable "%s" ("%s") setter method which must be defined',
                $key,
                'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key))),
                $setter
            ));
        }
    }

    /**
     * Get a configuration property
     *
     * @see ParameterObject::__get()
     *
     * @param string $key
     * @throws Exception\BadMethodCallException
     * @return mixed
     */
    public function __get($key)
    {
        $getter = 'get' . str_replace('_', '', $key);

        if (is_callable([$this, $getter])) {
            return $this->{$getter}();
        }

        throw new Exception\BadMethodCallException(sprintf(
            'The option "%s" does not have a callable "%s" getter method which must be defined',
            $key,
            'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)))
        ));
    }

    /**
     * Test if a configuration property is null
     *
     * @see ParameterObject::__isset()
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        $getter = 'get' . str_replace('_', '', $key);

        return method_exists($this, $getter) && null !== $this->__get($key);
    }

    /**
     * Set a configuration property to NULL
     *
     * @see ParameterObject::__unset()
     *
     * @param string $key
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public function __unset($key)
    {
        try {
            $this->__set($key, null);
        } catch (Exception\BadMethodCallException $e) {
            throw new Exception\InvalidArgumentException(
                'The class property $' . $key . ' cannot be unset as'
                . ' NULL is an invalid value for it',
                0,
                $e
            );
        }
    }
}
