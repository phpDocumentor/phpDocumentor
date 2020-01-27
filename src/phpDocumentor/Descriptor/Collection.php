<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use function array_merge;
use function count;

/**
 * Represents an easily accessible collection of elements.
 *
 * The goal for this class is to allow Descriptors to be easily retrieved and set so that interaction in
 * templates becomes easier.
 *
 * @template-implements ArrayAccess<string|int, mixed>
 * @template-implements IteratorAggregate<string|int, mixed>
 */
class Collection implements Countable, IteratorAggregate, ArrayAccess
{
    /** @var mixed[] $items */
    protected $items = [];

    /**
     * Constructs a new collection object with optionally a series of items, generally Descriptors.
     *
     * @param DescriptorAbstract[]|mixed[] $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * Adds a new item to this collection, generally a Descriptor.
     *
     * @param DescriptorAbstract|mixed $item
     */
    public function add($item) : void
    {
        $this->items[] = $item;
    }

    /**
     * Sets a new object onto the collection or clear it using null.
     *
     * @param string|int                    $index An index value to recognize this item with.
     * @param DescriptorAbstract|mixed|null $item  The item to store, generally a Descriptor but may be something else.
     */
    public function set($index, $item) : void
    {
        $this->offsetSet($index, $item);
    }

    /**
     * Retrieves a specific item from the Collection with its index.
     *
     * Please note that this method (intentionally) has the side effect that whenever a key does not exist that it will
     * be created with the value provided by the $valueIfEmpty argument. This will allow for easy initialization during
     * tree building operations.
     *
     * @param string|int $index
     * @param mixed      $valueIfEmpty If the index does not exist it will be created with this value and returned.
     *
     * @return mixed The contents of the element with the given index and the provided default if the key doesn't exist.
     */
    public function get($index, $valueIfEmpty = null)
    {
        if (!$this->offsetExists($index) && $valueIfEmpty !== null) {
            $this->offsetSet($index, $valueIfEmpty);
        }

        return $this->offsetGet($index);
    }

    /**
     * Retrieves all items from this collection as PHP Array.
     *
     * @return mixed[]
     */
    public function getAll() : array
    {
        return $this->items;
    }

    /**
     * Retrieves an iterator to traverse this object.
     *
     * @return ArrayIterator<string|int, mixed>
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Returns a count of the number of elements in this collection.
     */
    public function count() : int
    {
        return count($this->items);
    }

    /**
     * Empties the collection.
     */
    public function clear() : void
    {
        $this->items = [];
    }

    /**
     * Retrieves an item as if it were a property of the collection.
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * Checks whether an item in this collection exists.
     *
     * @param string|int $offset The index to check on.
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Retrieves an item from the collection with the given index.
     *
     * @param string|int $offset The offset to retrieve.
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->items[$offset] : null;
    }

    /**
     * Sets an item at the given index.
     *
     * @param string|int|null $offset The offset to assign the value to.
     * @param mixed           $value  The value to set.
     *
     * @throws InvalidArgumentException If the key is null or an empty string.
     */
    public function offsetSet($offset, $value) : void
    {
        if ($offset === '' || $offset === null) {
            throw new InvalidArgumentException('The key of a collection must always be set');
        }

        $this->items[$offset] = $value;
    }

    /**
     * Removes an item with the given index from the collection.
     *
     * @param string|int $offset The offset to unset.
     */
    public function offsetUnset($offset) : void
    {
        unset($this->items[$offset]);
    }

    /**
     * Returns a new collection with the items from this collection and the provided combined.
     */
    public function merge(self $collection) : Collection
    {
        return new self(array_merge($this->items, $collection->getAll()));
    }
}
