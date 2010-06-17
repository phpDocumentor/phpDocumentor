<?php

class BidiArrayIterator implements Countable, ArrayAccess, Serializable, SeekableIterator
{
  protected $store = array();
  protected $key_index = array();

  public function __construct(array $data)
  {
    $this->store = $data;
    $this->rewind();

    $this->key_index = array_keys($this->store);
    $this->key_index = array_flip($this->key_index);
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
    unset($this->store[$offset]);
  }

  public function offsetSet($offset, $value)
  {
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
    return reset($this->store);
  }

  public function valid()
  {
    return key($this->store) === null ? false : true;
  }

  public function key()
  {
    return key($this->store);
  }

  public function next()
  {
    return next($this->store);
  }

  public function previous()
  {
    return prev($this->store);
  }

  public function current()
  {
    return current($this->store);
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
  public function seek($position)
  {
    // item does not exist; move to an invalid location
    if (($position === null) || !isset($this->store[$position]))
    {
      $this->rewind();
      $this->previous();
      return;
    }

    // determine distance from current pointer position, first item and last
    // then move from that position
    $current_index = isset($this->key_index[$this->key()]) ? $this->key_index[$this->key()] : 99999999999999999999;
    $distance_from_start   = $this->key_index[$position];
    $distance_from_end     = (count($this->key_index) - 1) - $this->key_index[$position];
    $distance_from_current = $this->key_index[$position] - $current_index;

    if (($distance_from_start < $distance_from_end) && ($distance_from_start < abs($distance_from_current)))
    {
      // if the distance to the start is smallest; start there
      $this->rewind();
      while (($position !== $this->key()) && $this->valid())
      {
        $this->next();
      }

      return $this->current();
    }
    else
    if (($distance_from_end < $distance_from_start) && ($distance_from_end < abs($distance_from_current)))
    {
      // if the distance to the end is smallest; start there
      end($this->store);
      while (($position !== $this->key()) && $this->valid())
      {
        $this->previous();
      }

      return $this->current();
    }
    else
    {
      // if the distance to the current is smallest; start there and detect the direction
      $method_name = ($distance_from_current < 0) ? 'previous' : 'next';

      while (($position !== $this->key()) && $this->valid())
      {
        $this->$method_name();
      }

      return $this->current();
    }

  }

}

class Nabu_TokenIterator extends BidiArrayIterator
{
  public function  __construct($array)
  {
    // convert to token objects
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

//  public function prev()
//  {
//    // TODO: BIG ASSUMPTION! This assumes the keys are always ordered!
//    $key = $this->key();
//    if (!isset($keys[$key - 1]))
//    {
//      return false;
//    }
//
//    $this->seek($keys[$key - 1]);
//    return false;
//  }
//
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
