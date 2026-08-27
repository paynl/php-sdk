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
    /**
     * @var array
     */
    protected array $elements = [];

    /**
     * Create a collection.
     */
    public function __construct(array $elements = [])
    {
        $this->elements = $elements;
    }

    /**
     * Add an element to the collection.
     */
    public function add($element): bool
    {
        $this->elements[] = $element;
        return true;
    }

    /**
     * Set an element at the specified key.
     */
    public function set($key, $value): void
    {
        $this->elements[$key] = $value;
    }

    /**
     * Remove and return the element at the specified key.
     */
    public function remove($key)
    {
        if (array_key_exists($key, $this->elements) === false) {
            return null;
        }

        $removed = $this->elements[$key];
        unset($this->elements[$key]);

        return $removed;
    }

    /**
     * Remove the first matching element.
     */
    public function removeElement($element): bool
    {
        $key = array_search($element, $this->elements, true);
        if ($key === false) {
            return false;
        }

        unset($this->elements[$key]);
        return true;
    }

    /**
     * Remove all elements from the collection.
     */
    public function clear(): void
    {
        $this->elements = [];
    }

    /**
     * Count the elements in the collection.
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * Return all elements as an array.
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * Apply a callback to every element and return the resulting collection.
     */
    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->elements));
    }

    /**
     * Return an iterator for the collection.
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }

    /**
     * Determine whether an offset exists.
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->elements);
    }

    /**
     * Return the element at an offset.
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->elements[$offset] ?? null;
    }

    /**
     * Set the element at an offset.
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->add($value);
            return;
        }

        $this->set($offset, $value);
    }

    /**
     * Remove the element at an offset.
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }
}
