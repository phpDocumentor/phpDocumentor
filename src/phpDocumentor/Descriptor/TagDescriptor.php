<?php

namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\DocBlock\Tag;

class TagDescriptor
{
    protected $name;
    protected $description;

    public function __construct(Tag $reflectionTag)
    {
        $this->name        = $reflectionTag->getName();
        $this->description = $reflectionTag->getDescription();
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
