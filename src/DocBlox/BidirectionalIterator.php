<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Iterator
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Iterator class responsible for navigating through an array forwards and backwards.
 *
 * @category DocBlox
 * @package  Iterator
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_BidirectionalIterator implements Countable, ArrayAccess, Serializable, SeekableIterator
{
  /**
   * Array containing the items to store
   *
   * @var mixed[]
   */
  protected $store         = array();

  /**
   * Array matching 'store keys' => 'sequence/pointer number'
   *
   * @var int[]
   */
  protected $key_index     = array();

  /**
   * Array matching 'sequence/pointer number' => 'store keys'
   *
   * @var int[]
   */
  protected $pointer_index = array();

  /**
   * Internal array pointer; contains the actual key in the datastore
   *
   * @var int
   */
  protected $pointer       = null;

  /**
   * Initializes the iterator and populate the pointer array.
   *
   * @param mixed[] $data
   */
  public function __construct(array $data)
  {
    $this->setDataStore($data);
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
    $this->setDataStore(unserialize($serialized));
  }

  /**
   * Serialize the store.
   *
   * @return string
   */
  public function serialize()
  {
    return serialize($this->store);
  }

  /**
   * Populate the data store and initialize the pointers.
   *
   * @param array $data
   *
   * @return void
   */
  protected function setDataStore(array $data)
  {
    $this->store = $data;

    // cache sequence and sequence lookup hashes
    $this->pointer_index = array_keys($this->store);
    $this->key_index = array_flip($this->pointer_index);

    $this->rewind();
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
    throw new Exception('BidiArrayIterator does not support adding or removing of items');
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
    if (!$this->offsetExists($offset))
    {
      throw new Exception('BidiArrayIterator does not support adding or removing of items');
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
    return isset($this->store[$offset]) ? $this->store[$offset] : null;
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
    return isset($this->store[$offset]);
  }

  /**
   * Returns a count of the items contained in this iterator.
   *
   * @return int
   */
  public function count()
  {
    return count($this->store);
  }

  /**
   * Sets the array pointer to the first item and returns that item.
   *
   * @return mixed|null
   */
  public function rewind()
  {
    $this->pointer = isset($this->pointer_index[0]) ? $this->pointer_index[0] : false;

    return $this->offsetGet($this->pointer);
  }

  /**
   * Returns true if the pointer is at an existing item.
   *
   * @return boolean
   */
  public function valid()
  {
    return (($this->pointer !== null) && $this->offsetExists($this->pointer));
  }

  /**
   * Returns the key of the currently active item.
   *
   * @return int|null
   */
  public function key()
  {
    return $this->pointer;
  }

  /**
   * Shifts the pointer to the next item in the sequence and returns the newly selected item; returns
   * false when none found.
   *
   * @return bool|mixed|null
   */
  public function next()
  {
    // get the sequence number; if it does not exist return false to indicate invalid position
    $sequence_nr = isset($this->key_index[$this->pointer]) ? $this->key_index[$this->pointer] : null;
    if ($sequence_nr === null)
    {
      return false;
    }

    // add 1 to the sequence number and get the new pointer location
    $sequence_nr++;
    $this->pointer = isset($this->pointer_index[$sequence_nr]) ? $this->pointer_index[$sequence_nr] : null;
    if ($this->pointer === null)
    {
      return false;
    }

    // return data
    return $this->current();
  }

  /**
   * Shifts the pointer to the previous item in the sequence and returns the newly selected item; returns
   * false when none found.
   *
   * @return bool|mixed|null
   */
  public function previous()
  {
    // get the sequence number; if it does not exist return false to indicate invalid position
    $sequence_nr = isset($this->key_index[$this->pointer]) ? $this->key_index[$this->pointer] : null;
    if ($sequence_nr === null)
    {
      return false;
    }

    // subtract 1 to the sequence number and get the new pointer location
    $sequence_nr--;
    $this->pointer = isset($this->pointer_index[$sequence_nr]) ? $this->pointer_index[$sequence_nr] : null;
    if ($this->pointer === null)
    {
      return false;
    }

    // return data
    return $this->current();
  }

  /**
   * Returns the currently selected item.
   *
   * @return mixed
   */
  public function current()
  {
    return $this->offsetGet($this->pointer);
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
    $this->pointer = $key;

    return $this->offsetGet($this->key());
  }

}