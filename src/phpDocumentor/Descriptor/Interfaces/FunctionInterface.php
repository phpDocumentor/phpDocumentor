<?php
namespace phpDocumentor\Descriptor\Interfaces;

interface FunctionInterface extends BaseInterface
{
    /**
     * @return \ArrayObject
     */
    public function getArguments();
}
