<?php

declare(strict_types=1);

namespace PayNL\Sdk\Mapper;

use PayNL\Sdk\Exception\InvalidArgumentException;

/**
 * Class AbstractMapper
 *
 * @package PayNL\Sdk\Mapper
 */
abstract class AbstractMapper implements MapperInterface
{
    /**
     * @var array
     */
    protected $map = [];

    /**
     * AbstractMapper constructor.
     *
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @return array
     */
    public function getMapping(): array
    {
        return $this->map;
    }

    /**
     * @param string|object $target
     *
     * @throws InvalidArgumentException
     */
    public function getSource(string|object $target): string
    {
        if (is_object($target)) {
            $target = get_class($target);
        }

        return array_search($target, $this->map, true) ?: '';
    }

    /**
     * @param string|object $source
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function getTarget(string|object $source): string
    {
        if (is_object($source)) {
            $source = get_class($source);
        }

        return $this->map[$source] ?? '';
    }

}
