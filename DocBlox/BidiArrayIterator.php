<?php
class DocBlox_BidiArrayIterator implements Countable, ArrayAccess, Serializable, SeekableIterator
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

  public function __construct(array $data)
  {
    $this->store = $data;

    // cache sequence and sequence lookup hashes
    $this->pointer_index = array_keys($this->store);
    $this->key_index     = array_flip($this->pointer_index);

    $this->rewind();
  }

  public function unserialize($serialized)
  {
    $this->store = unserialize($serialized);
  }

  public function serialize()
  {
    return serialize($this->store);
  }

  public function offsetUnset($offset)
  {
    throw new Exception('BidiArrayIterator does not support adding or removing of items');
  }

  public function offsetSet($offset, $value)
  {
    if (!$this->offsetExists($offset))
    {
      throw new Exception('BidiArrayIterator does not support adding or removing of items');
    }

    $this->store[$offset] = $value;
  }

  public function offsetGet($offset)
  {
    return isset($this->store[$offset]) ? $this->store[$offset] : null;
  }

  public function offsetExists($offset)
  {
    return isset($this->store[$offset]);
  }

  public function count()
  {
    return count($this->store);
  }

  public function rewind()
  {
    $this->pointer = isset($this->pointer_index[0]) ? $this->pointer_index[0] : false;

    return $this->offsetGet($this->pointer);
  }

  public function valid()
  {
    return (($this->pointer !== null) && $this->offsetExists($this->pointer));
  }

  public function key()
  {
    return $this->pointer;
  }

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
    return $this->offsetGet($this->pointer);
  }

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
    return $this->offsetGet($this->pointer);
  }

  /**
   * @return DocBlox_Token
   */
  public function current()
  {
    return $this->offsetGet($this->pointer);
  }

  /**
   *
   *
   * NOTE: this function is used A LOT during the reflection process.
   * This should be as high-performant as possible and ways should be devised to not use it.
   *
   * @param int|string $position
   *
   * @return mixed
   */
  public function seek($key)
  {
    $this->pointer = $key;

    return $this->offsetGet($this->key());
  }

}