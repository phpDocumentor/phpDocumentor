<?php
namespace phpDocumentor\Descriptor;

class InterfaceDescriptor extends DescriptorAbstract
{
    /** @var \ArrayObject $extends */
    protected $extends;

    /** @var \ArrayObject $methods */
    protected $methods;

    public function __construct()
    {
        parent::__construct();

        $this->setParentInterfaces(new \ArrayObject());
        $this->setMethods(new \ArrayObject());
    }

    /**
     * @param \ArrayObject $extends
     */
    protected function setParentInterfaces($extends)
    {
        $this->extends = $extends;
    }

    /**
     * @return \ArrayObject
     */
    public function getParentInterfaces()
    {
        return $this->extends;
    }

    /**
     * @param \ArrayObject $methods
     */
    protected function setMethods($methods)
    {
        $this->methods = $methods;
    }

    /**
     * @return \ArrayObject
     */
    public function getMethods()
    {
        return $this->methods;
    }
}
