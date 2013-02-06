<?php
namespace phpDocumentor\Descriptor\Interfaces;

interface ConstantInterface extends BaseInterface
{
    public function setType($type);

    public function getType();

    public function setValue($value);

    public function getValue();
}
