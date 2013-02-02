<?php
namespace phpDocumentor\Descriptor;

class ConstantDescriptor extends DescriptorAbstract
{
    protected $type;
    protected $value;

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
