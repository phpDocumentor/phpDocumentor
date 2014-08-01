<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

/**
 * Represents an easily accessible collection of elements.
 *
 * The goal for this class is to allow Descriptors to be easily retrieved and set so that interaction in
 * templates becomes easier.
 */
class Collection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /** @var mixed[] $items */
    protected $items = array();

    /**
     * Constructs a new collection object with optionally a series of items, generally Descriptors.
     *
     * @param DescriptorAbstract[]|mixed[] $items
     */
    public function __construct($items = array())
    {
        $this->items = $items;
    }

    /**
     * Adds a new item to this collection, generally a Descriptor.
     *
     * @param DescriptorAbstract|mixed $item
     *
     * @return void
     */
    public function add($item)
    {
        $this->items[] = $item;
    }

    /**
     * Sets a new object onto the collection or clear it using null.
     *
     * @param string|integer                $index An index value to recognize this item with.
     * @param DescriptorAbstract|mixed|null $item  The item to store, generally a Descriptor but may be something else.
     *
     * @return void
     */
    public function set($index, $item)
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
     * @param string|integer $index
     * @param mixed          $valueIfEmpty If the index does not exist it will be created with this value and returned.
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
    public function getAll()
    {
        return $this->items;
    }

    /**
     * Retrieves an iterator to traverse this object.
     *
     * @return \Traversable|\ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Returns a count of the number of elements in this collection.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Empties the collection.
     *
     * @return void
     */
    public function clear()
    {
        $this->items = array();
    }

    /**
     * Retrieves an item as if it were a property of the collection.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Checks whether an item in this collection exists.
     *
     * @param string|integer $offset The index to check on.
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * Retrieves an item from the collection with the given index.
     *
     * @param string|integer $offset The offset to retrieve.
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return ($this->offsetExists($offset)) ? $this->items[$offset] : null;
    }

    /**
     * Sets an item at the given index.
     *
     * @param string|integer $offset The offset to assign the value to.
     * @param mixed          $value  The value to set.
     *
     * @throws \InvalidArgumentException if the key is null or an empty string.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === '' || $offset === null) {
            throw new \InvalidArgumentException('The key of a collection must always be set');
        }

        $this->items[$offset] = $value;
    }

    /**
     * Removes an item with the given index from the collection.
     *
     * @param string|integer $offset The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * Returns a new collection with the items from this collection and the provided combined.
     *
     * @param Collection $collection
     *
     * @return Collection
     */
    public function merge(Collection $collection)
    {
        return new Collection(array_merge($this->items, $collection->getAll()));
    }
}
