<?php
namespace phpDocumentor\Descriptor;

class NamespaceDescriptor extends DescriptorAbstract
{
    /** @var \ArrayObject $namespaces*/
    protected $namespaces;

    /** @var \ArrayObject $functions*/
    protected $functions;

    /** @var \ArrayObject $constants*/
    protected $constants;

    /** @var \ArrayObject $classes*/
    protected $classes;

    /** @var \ArrayObject $interfaces*/
    protected $interfaces;

    /** @var \ArrayObject $traits*/
    protected $traits;

    public function __construct()
    {
        $this->setNamespaces(new \ArrayObject());
        $this->setFunctions(new \ArrayObject());
        $this->setConstants(new \ArrayObject());
        $this->setClasses(new \ArrayObject());
        $this->setInterfaces(new \ArrayObject());
        $this->setTraits(new \ArrayObject());
    }

    /**
     * @param \ArrayObject $classes
     */
    protected function setClasses($classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return \ArrayObject
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param \ArrayObject $constants
     */
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

    /**
     * @param \ArrayObject $functions
     */
    protected function setFunctions($functions)
    {
        $this->functions = $functions;
    }

    /**
     * @return \ArrayObject
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * @param \ArrayObject $interfaces
     */
    protected function setInterfaces($interfaces)
    {
        $this->interfaces = $interfaces;
    }

    /**
     * @return \ArrayObject
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @param \ArrayObject $namespaces
     */
    protected function setNamespaces($namespaces)
    {
        $this->namespaces = $namespaces;
    }

    /**
     * @return \ArrayObject
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * @param \ArrayObject $traits
     */
    protected function setTraits($traits)
    {
        $this->traits = $traits;
    }

    /**
     * @return \ArrayObject
     */
    public function getTraits()
    {
        return $this->traits;
    }
}
