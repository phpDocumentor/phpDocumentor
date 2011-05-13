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
class DocBlox_BidirectionalIterator extends ArrayIterator
{

    public function offsetGet($i)
    {
        if (($i < 0) || ($i >= $this->count())) {
            return false;
        }

        return parent::offsetGet($i);
    }

    public function next()
    {
        try {
            parent::next();
            return $this->current();
        } catch (OutOfBoundsException $e) {
            return false;
        }
    }

    public function previous()
    {
        try {
            if ($this->key() - 1 < 0) {
                return false;
            }

            $this->seek($this->key() -1);
            return $this->current();
        } catch(OutOfBoundsException $e) {
            return false;
        }
    }

    public function seek($i)
    {
        try {
            parent::seek($i);
            return $this->current();
        } catch(OutOfBoundsException $e) {
            return false;
        }
    }
}