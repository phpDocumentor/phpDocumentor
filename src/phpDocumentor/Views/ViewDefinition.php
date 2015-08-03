<?php

namespace phpDocumentor\Views;

class ViewDefinition
{
    /** @var string */
    private $name;

    /** @var ViewType */
    private $type;

    /** @var Filter|null */
    private $filter;

    /** @var string[] */
    private $properties = [];

    public function __construct($name, ViewType $type, $filter = null, $properties = [])
    {
        $this->name       = $name;
        $this->type       = $type;
        $this->filter     = $filter;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ViewType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Filter|null
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return string[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
