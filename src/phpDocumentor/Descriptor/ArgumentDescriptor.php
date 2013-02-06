<?php
namespace phpDocumentor\Descriptor;

class ArgumentDescriptor extends DescriptorAbstract implements Interfaces\ArgumentInterface
{
    protected $type;
    protected $default;
    protected $byReference = false;

    public function setType($name)
    {
        $this->type = $name;
    }

    public function getType()
    {
        return $this->name;
    }

    public function setDefault($value)
    {
        $this->default = $value;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function setByReference($byReference)
    {
        $this->byReference = $byReference;
    }

    public function isByReference()
    {
        return $this->byReference;
    }
}
