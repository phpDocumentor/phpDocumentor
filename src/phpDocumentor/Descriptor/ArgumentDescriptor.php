<?php
namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;

class ArgumentDescriptor extends DescriptorAbstract implements Interfaces\ArgumentInterface
{
    /** @var TypeCollection $type */
    protected $type;

    /** @var bool $default */
    protected $default;

    /** @var bool $byReference */
    protected $byReference = false;

    public function setType(TypeCollection $type)
    {
        $this->type = $type;
    }

    /**
     * @return TypeCollection
     */
    public function getType()
    {
        return $this->type;
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
