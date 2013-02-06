<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Reflection\DocBlock\Type\Collection;

interface ArgumentInterface extends BaseInterface
{
    public function setType(Collection $type);

    /**
     * @return Collection
     */
    public function getType();

    public function setDefault($value);

    public function getDefault();

    public function setByReference($byReference);

    public function isByReference();
}
