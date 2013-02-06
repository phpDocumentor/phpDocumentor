<?php
namespace phpDocumentor\Descriptor;

class TraitDescriptor extends DescriptorAbstract implements Interfaces\TraitInterface
{
    /** @var \ArrayObject $properties */
    protected $properties;

    /** @var \ArrayObject $methods */
    protected $methods;

    public function __construct()
    {
        parent::__construct();

        $this->setProperties(new \ArrayObject());
        $this->setMethods(new \ArrayObject());
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

    /**
     * @param \ArrayObject $properties
     */
    protected function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return \ArrayObject
     */
    public function getProperties()
    {
        return $this->properties;
    }


}
