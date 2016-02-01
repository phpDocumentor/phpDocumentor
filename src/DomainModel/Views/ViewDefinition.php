<?php

namespace phpDocumentor\DomainModel\Views;

class ViewDefinition
{
    /** @var string */
    private $name;

    /** @var ViewType */
    private $type;

    /** @var Filter[] */
    private $filters = [];

    /** @var string[] */
    private $properties = [];

    public function __construct($name, ViewType $type, array $filters = [], array $properties = [])
    {
        $this->name       = $name;
        $this->type       = $type;
        $this->filters    = $filters;
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
     * @return Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return string[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
