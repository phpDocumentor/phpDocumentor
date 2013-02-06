<?php
namespace phpDocumentor\Descriptor\Interfaces;

interface FunctionInterface extends BaseInterface
{
    /**
     * @return \ArrayObject
     */
    public function getArguments();

    /**
     * @param string $return
     */
    public function setReturn($return);

    /**
     * @return string
     */
    public function getReturn();
}
