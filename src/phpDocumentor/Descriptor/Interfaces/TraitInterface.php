<?php
namespace phpDocumentor\Descriptor\Interfaces;

interface TraitInterface extends BaseInterface
{
    /**
     * @return \ArrayObject
     */
    public function getMethods();

    /**
     * @return \ArrayObject
     */
    public function getProperties();
}
