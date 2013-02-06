<?php
namespace phpDocumentor\Descriptor;

class ClassDescriptor extends DescriptorAbstract implements Interfaces\ClassInterface
{
    protected $extends;

    /** @var \ArrayObject $implements */
    protected $implements;

    protected $abstract = false;
    protected $final = false;

    /** @var \ArrayObject $constants */
    protected $constants;

    /** @var \ArrayObject $properties */
    protected $properties;

    /** @var \ArrayObject $methods */
    protected $methods;

    public function __construct()
    {
        parent::__construct();

        $this->setInterfaces(new \ArrayObject());
        $this->setConstants(new \ArrayObject());
        $this->setProperties(new \ArrayObject());
        $this->setMethods(new \ArrayObject());
    }

    public function setParentClass($extends)
    {
        $this->extends = $extends;
    }

    public function getParentClass()
    {
        return $this->extends;
    }

    public function setFinal($final)
    {
        $this->final = $final;
    }

    public function isFinal()
    {
        return $this->final;
    }

    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;
    }

    public function isAbstract()
    {
        return $this->abstract;
    }

    protected function setConstants($constants)
    {
        $this->constants = $constants;
    }

    /**
     * @return \ArrayObject
     */
    public function getConstants()
    {
        return $this->constants;
    }

    protected function setInterfaces($implements)
    {
        $this->implements = $implements;
    }

    public function getInterfaces()
    {
        return $this->implements;
    }

    protected function setMethods($methods)
    {
        $this->methods = $methods;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    protected function setProperties($properties)
    {
        $this->properties = $properties;
    }

    public function getProperties()
    {
        return $this->properties;
    }


}