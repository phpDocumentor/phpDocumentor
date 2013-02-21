<?php

namespace phpDocumentor\Descriptor;

class Collection implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /** @var mixed[] $items */
    protected $items = array();

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Constructs a new collection object with optionally a series of Descriptors.
     *
     * @param mixed[] $items
     */
    public function __construct($items = array())
    {
        $this->items = $items;
    }

    /**
     * Adds a new Descriptor.
     *
     * @param mixed $item
     *
     * @return void
     */
    public function add($item)
    {
        $this->items[] = $item;
    }

    /**
     * Sets a new object onto the collection.
     *
     * @param string|integer $index
     * @param mixed          $item
     *
     * @return void
     */
    public function set($index, $item)
    {
        $this->offsetSet($index, $item);
    }

    public function get($index, $valueIfEmpty = null)
    {
        if (!$this->offsetExists($index)) {
            $this->offsetSet($index, $valueIfEmpty);
        }

        return $this->offsetGet($index);
    }

    /**
     * @return mixed[]
     */
    public function getAll()
    {
        return $this->items;
    }

    public function count()
    {
        return count($this->items);
    }

    public function clear()
    {
        $this->items = array();
    }

    /**
     * @param $name
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
     * Item to retrieve.
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
     * Offset to set
     *
     * @param string|integer $offset The offset to assign the value to.
     * @param mixed          $value  The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->items[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @param string|integer $offset The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}
