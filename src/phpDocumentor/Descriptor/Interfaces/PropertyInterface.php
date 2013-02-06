<?php
namespace phpDocumentor\Descriptor\Interfaces;

interface PropertyInterface extends BaseInterface
{
    public function setDefault($default);

    public function getDefault();

    public function setStatic($static);

    public function isStatic();

    public function setType($type);

    public function getType();

    public function setVisibility($visibility);

    public function getVisibility();
}
