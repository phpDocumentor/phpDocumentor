<?php
namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\DocBlock\Type;

class ConstantDescriptor extends DescriptorAbstract implements Interfaces\ConstantInterface
{
    /** @var Type\Collection $type */
    protected $type;

    /** @var string $value */
    protected $value;

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

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
