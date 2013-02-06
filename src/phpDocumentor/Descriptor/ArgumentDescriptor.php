<?php
namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\DocBlock\Type\Collection;

class ArgumentDescriptor extends DescriptorAbstract implements Interfaces\ArgumentInterface
{
    /** @var Collection $type */
    protected $type;

    /** @var bool $default */
    protected $default;

    /** @var bool $byReference */
    protected $byReference = false;

    public function setType(Collection $type)
    {
        $this->type = $type;
    }

    /**
     * @return Collection
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
