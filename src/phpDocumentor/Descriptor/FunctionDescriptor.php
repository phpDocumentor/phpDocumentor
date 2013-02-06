<?php
namespace phpDocumentor\Descriptor;

class FunctionDescriptor extends DescriptorAbstract implements Interfaces\FunctionInterface
{
    /** @var \ArrayObject $arguments */
    protected $arguments;

    /** @var string $return */
    protected $return;

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

    /**
     * @param string $return
     */
    public function setReturn($return)
    {
        $this->return = $return;
    }

    /**
     * @return string
     */
    public function getReturn()
    {
        return $this->return;
    }
}
