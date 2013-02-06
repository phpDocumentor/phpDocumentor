<?php
namespace phpDocumentor\Descriptor;

use phpDocumentor\Reflection\DocBlock\Type\Collection;

class ConstantDescriptor extends DescriptorAbstract implements Interfaces\ConstantInterface
{
    /** @var Collection $type */
    protected $type;

    /** @var string $value */
    protected $value;

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

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
