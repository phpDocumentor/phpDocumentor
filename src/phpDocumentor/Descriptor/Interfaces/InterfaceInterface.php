<?php
namespace phpDocumentor\Descriptor\Interfaces;

interface InterfaceInterface extends BaseInterface
{
    /**
     * @return \ArrayObject
     */
    public function getParentInterfaces();

    /**
     * @return \ArrayObject
     */
    public function getMethods();
}
