<?php
namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Reflection\DocBlock\Type\Collection;

interface PropertyInterface extends BaseInterface
{
    public function setDefault($default);

    public function getDefault();

    public function setStatic($static);

    public function isStatic();

    public function setType(Collection $type);

    /**
     * @return Collection
     */
    public function getType();

    public function setVisibility($visibility);

    public function getVisibility();
}
