<?php
namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;

class ArgumentDescriptor extends DescriptorAbstract implements Interfaces\ArgumentInterface
{
    /** @var string[] $type */
    protected $types;

    /** @var bool $default */
    protected $default;

    /** @var bool $byReference */
    protected $byReference = false;

    /**
     * @param string[] $types
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }

    /**
     * @return string[]
     */
    public function getTypes()
    {
        return $this->types;
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
