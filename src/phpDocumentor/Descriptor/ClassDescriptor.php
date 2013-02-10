<?php
namespace phpDocumentor\Descriptor;

class ClassDescriptor extends DescriptorAbstract implements Interfaces\ClassInterface
{
    protected $extends;

    /** @var Collection $implements */
    protected $implements;

    protected $abstract = false;
    protected $final = false;

    /** @var Collection $constants */
    protected $constants;

    /** @var Collection $properties */
    protected $properties;

    /** @var Collection $methods */
    protected $methods;

    public function __construct()
    {
        parent::__construct();

        $this->setInterfaces(new Collection());
        $this->setConstants(new Collection());
        $this->setProperties(new Collection());
        $this->setMethods(new Collection());
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
     * @return Collection
     */
    public function getConstants()
    {
        return $this->constants;
    }

    public function setInterfaces($implements)
    {
        $this->implements = $implements;
    }

    public function getInterfaces()
    {
        return $this->implements;
    }

    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    public function getProperties()
    {
        return $this->properties;
    }
}
