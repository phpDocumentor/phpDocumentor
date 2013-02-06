<?php
namespace phpDocumentor\Descriptor\Interfaces;

interface ArgumentInterface extends BaseInterface
{
    public function setType($name);
    public function getType();
    public function setDefault($value);
    public function getDefault();
    public function setByReference($byReference);
    public function isByReference();
}
