<?php
namespace phpDocumentor\Descriptor;

class FunctionDescriptor extends DescriptorAbstract implements Interfaces\FunctionInterface
{
    /** @var \ArrayObject $arguments */
    protected $arguments;

    public function __construct()
    {
        $this->setArguments(new \ArrayObject());
    }

    /**
     * @param \ArrayObject $arguments
     */
    protected function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @return \ArrayObject
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
