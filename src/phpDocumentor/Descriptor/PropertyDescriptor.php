<?php
namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\DocBlock\Type;

class PropertyDescriptor extends DescriptorAbstract implements Interfaces\PropertyInterface
{
    /** @var Type\Collection $type */
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

    public function setType(Type\Collection $type)
    {
        $this->type = $type;
    }

    /**
     * @return Type\Collection
     */
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
