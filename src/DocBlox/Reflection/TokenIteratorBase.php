<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Core
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Iterator class responsible for navigating through an array forwards and backwards.
 *
 * @category   DocBlox
 * @package    Core
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Reflection_TokenIteratorBase implements Countable, ArrayAccess, Serializable, SeekableIterator
{
    /** @var int Current key value */
    protected $key   = 0;

    /** @var int Count of items */
    protected $count = 0;

    /** @var mixed[] Contents for this iterator */
    protected $store = array();

    /** @var mixed Current value */
    protected $current = null;

    /**
     * Initializes the iterator and populate the pointer array.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->store = $data;
        $this->count = count($data);
        $this->current = reset($this->store);
    }

    /**
     * Load a serialized store and populate the pointers.
     *
     * @param string $serialized
     *
     * @return void
     */
    public function unserialize($serialized)
    {
    }

    /**
     * Serialize the store.
     *
     * @return string
     */
    public function serialize()
    {
    }

    /**
     * Due to the pointers it is not allowed to remove an item from the array.
     *
     * @throws Exception
     * @param int $offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException(
            'This iterator does not allow items to be unset'
        );
    }

    /**
     * Due to the pointers it is not allowed to add an item onto the array.
     *
     * @throws Exception
     * @param integer $offset
     * @param string  $value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (!isset($this->store[$offset]))
        {
            throw new BadMethodCallException(
                'This iterator does not allow new items to be added'
            );
        }

        $this->store[$offset] = $value;
    }

    /**
     * Returns the value from the given $offset; or null when no item could be found.
     *
     * @param string $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        if(!isset($this->store[$offset])) {
            return false;
        }
        return $this->store[$offset];
    }

    /**
     * Returns true if an item exists.
     *
     * @param integer $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return ($offset >= 0 && $offset < $this->count);
    }

    /**
     * Returns a count of the items contained in this iterator.
     *
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Sets the array pointer to the first item and returns that item.
     *
     * @return mixed|null
     */
    public function rewind()
    {
        $this->current = $this->store[0];
        $this->key = 0;
    }

    /**
     * Returns true if the pointer is at an existing item.
     *
     * @return boolean
     */
    public function valid()
    {
        return (bool)($this->current !== false);
    }

    /**
     * Returns the key of the currently active item.
     *
     * @return int|null
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * Shifts the pointer to the next item in the sequence and returns the newly selected item; returns
     * false when none found.
     *
     * @return bool|mixed|null
     */
    public function next()
    {
        $key = ++$this->key;
        $current = $this->offsetGet($key);
        $this->current = $current;

        return $current;
    }

    /**
     * Shifts the pointer to the previous item in the sequence and returns the newly selected item; returns
     * false when none found.
     *
     * @return bool|mixed|null
     */
    public function previous()
    {
        $this->key--;
        $this->current = ($this->key < 0) ? false : $this[$this->key];
        return $this->current;
    }

    /**
     * Returns the currently selected item.
     *
     * @return mixed
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Moves the pointer to a specific position in the store.
     *
     * NOTE: this function is used A LOT during the reflection process.
     * This should be as high-performance as possible and ways should be devised to not use it.
     *
     * @param int|string $key
     *
     * @return mixed
     */
    public function seek($key)
    {
        $this->key = $key;
        $this->current = (($this->key < 0) || ($this->key >= $this->count))
            ? false
            : $this[$this->key];
        return $this->current;
    }

}