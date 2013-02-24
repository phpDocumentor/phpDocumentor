<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Reflection\DocBlock\Type\Collection;

interface ArgumentInterface extends BaseInterface
{
    public function setTypes($types);

    /**
     * @return string[]
     */
    public function getTypes();

    public function setDefault($value);

    public function getDefault();

    public function setByReference($byReference);

    public function isByReference();
}
