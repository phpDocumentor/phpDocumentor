<?php

namespace phpDocumentor\Descriptor;

class Collection
{
    protected $items = array();

    public function __construct($items = array())
    {
        $this->items = $items;
    }

    public function add($item)
    {
        $this->items[] = $item;
    }

    public function set($index, $item)
    {
        $this->items[$index] = $item;
    }

    public function get($index, $valueIfEmpty = null)
    {
        if (!isset($this->items[$index])) {
            $this->items[$index] = $valueIfEmpty;
        }

        return $this->items[$index];
    }

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
}
