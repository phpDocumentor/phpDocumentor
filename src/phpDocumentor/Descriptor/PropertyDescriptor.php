<?php
namespace phpDocumentor\Descriptor;

class PropertyDescriptor extends DescriptorAbstract
{
    protected $type;
    protected $default;
    protected $static = false;
    protected $visibility = false;

    public function setDefault($default)
    {
        $this->default = $default;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function setStatic($static)
    {
        $this->static = $static;
    }

    public function isStatic()
    {
        return $this->static;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    public function getVisibility()
    {
        return $this->visibility;
    }
}
