<?php

class BidiArrayIterator implements Countable, ArrayAccess, Serializable, SeekableIterator
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
    $this->pointer = $this->pointer_index[0];

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

class Nabu_TokenIterator extends BidiArrayIterator
{
  public function  __construct($array)
  {
    // convert to token objects; converting up front is _faster_ than ad hoc conversion
    foreach ($array as &$token)
    {
      if ($token instanceof Nabu_Token)
      {
        continue;
      }

      $token = new Nabu_Token($token);
    }

    parent::__construct($array);
  }

  protected function gotoTokenByTypeInDirection($type, $direction = 'next', $max_count = 0, $stop_at = null)
  {
    if (!in_array($direction, array('next', 'previous')))
    {
      throw new Exception('The direction must be a string containing either "next" or "previous"');
    }

    $token = null;
    $found = false;
    $count = 0;
    $index = $this->key();

    if (!is_array($stop_at) && ($stop_at !== null))
    {
      $stop_at = array($stop_at);
    }

    // start with the next item
    $this->$direction();
    while ($this->valid())
    {
      $count++;
      $token = $this->current();
      if ($token->getType() == $type)
      {
        $found = true;
        break;
      }

      // the prev method returns false when there is nothing left to iterate
      $result = $this->$direction();
      if ($result === false ||
        (($max_count > 0) && ($count == $max_count)) ||
        (($stop_at !== null) && (in_array($token->getType(), $stop_at) || in_array($token->getContent(), $stop_at))))
      {
        break;
      }
    }

    // return to the last known position if none was found
    if (!$found)
    {
      $this->seek($index);
    }

    // return the result
    return $found ? $token : false;
  }

  protected function findByTypeInDirection($type, $direction = 'next', $max_count = 0, $stop_at = null)
  {
    // store current position
    $index = $this->key();

    // move to token (if found) and get that token
    $found = $this->gotoTokenByTypeInDirection($type, $direction, $max_count, $stop_at);

    // return to the last position
    $this->seek($index);

    // return the result
    return $found ? $found : null;
  }

  public function gotoNextByType($type, $max_count = 0, $stop_at = null)
  {
    return $this->gotoTokenByTypeInDirection($type, 'next', $max_count, $stop_at);
  }

  public function gotoPreviousByType($type, $max_count = 0, $stop_at = null)
  {
    return $this->gotoTokenByTypeInDirection($type, 'previous', $max_count, $stop_at);
  }

  public function findNextByType($type, $max_count = 0, $stop_at = null)
  {
    return $this->findByTypeInDirection($type, 'next', $max_count, $stop_at);
  }

  public function findPreviousByType($type, $max_count = 0, $stop_at = null)
  {
    return $this->findByTypeInDirection($type, 'previous', $max_count, $stop_at);
  }

  protected function getTokenIdsBetweenPair($start_literal, $end_literal)
  {
    // store current position
    $index = $this->key();
    $level = -1;
    $start = null;
    $end   = null;

    $this->next();
    while ($this->valid())
    {
      $token = $this->current();
      if (($token->getType() === null))
      {
        if($token->getContent() == $start_literal)
        {
          if ($level == -1)
          {
            $level++;
            $start = $this->key();
          }
          $level++;
          $this->next();
          continue;
        }
        elseif ($token->getContent() == $end_literal)
        {
          if ($level == -1)
          {
            // expect the first brace to be an opening brace
            break;
          }
          $level--;

          // reached the end!
          if ($level === 0)
          {
            $end = $this->key();
            break;
          }
          $this->next();
          continue;
        }
      }

      $this->next();
    }

    // return to the last position
    $this->seek($index);

    return array(
      $start,
      $end
    );
  }

  public function getTokenIdsOfBracePair()
  {
    return $this->getTokenIdsBetweenPair('{', '}');
  }

  public function getTokenIdsOfParenthesisPair()
  {
    return $this->getTokenIdsBetweenPair('(', ')');
  }

}
