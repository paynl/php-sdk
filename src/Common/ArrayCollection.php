<?php

declare(strict_types=1);

namespace PayNL\Sdk\Common;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Lightweight internal replacement for the small ArrayCollection surface used by the SDK.
 */
class ArrayCollection implements ArrayAccess, Countable, IteratorAggregate
{
    protected array $elements = [];

    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    public function add($element): bool
    {
        $this->elements[] = $element;
        return true;
    }

    public function set($key, $value): void
    {
        $this->elements[$key] = $value;
    }

    public function remove($key)
    {
        if (array_key_exists($key, $this->elements) === false) {
            return null;
        }

        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    public function removeElement($element): bool
    {
        $key = array_search($element, $this->elements, true);
        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);
        return true;
    }

    public function clear(): void
    {
        $this->elements = [];
    }

    public function count(): int
    {
        return count($this->elements);
    }

    public function toArray(): array
    {
        return $this->elements;
    }

    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->elements));
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->elements);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->elements[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->add($value);
            return;
        }

        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }
}
