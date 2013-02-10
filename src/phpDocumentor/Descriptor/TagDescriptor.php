<?php

namespace phpDocumentor\Descriptor;

class TagDescriptor
{
    protected $name;
    protected $description;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    protected function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
