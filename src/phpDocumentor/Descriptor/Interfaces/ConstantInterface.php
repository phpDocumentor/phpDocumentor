<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Reflection\DocBlock\Type\Collection;

interface ConstantInterface extends BaseInterface
{
    public function setType(Collection $type);

    /**
     * @return Collection
     */
    public function getType();

    public function setValue($value);

    public function getValue();
}
