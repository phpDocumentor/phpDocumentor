<?php
namespace phpDocumentor\Descriptor;

class NamespaceDescriptor extends DescriptorAbstract implements Interfaces\NamespaceInterface
{
    /** @var Collection $namespaces*/
    protected $namespaces;

    /** @var Collection $functions*/
    protected $functions;

    /** @var Collection $constants*/
    protected $constants;

    /** @var Collection $classes*/
    protected $classes;

    /** @var Collection $interfaces*/
    protected $interfaces;

    /** @var Collection $traits*/
    protected $traits;

    public function __construct()
    {
        $this->setNamespaces(new Collection());
        $this->setFunctions(new Collection());
        $this->setConstants(new Collection());
        $this->setClasses(new Collection());
        $this->setInterfaces(new Collection());
        $this->setTraits(new Collection());
    }

    /**
     * @param Collection $classes
     */
    protected function setClasses(Collection $classes)
    {
        $this->classes = $classes;
    }

    /**
     * @return Collection
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param Collection $constants
     */
    protected function setConstants(Collection $constants)
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

    /**
     * @param Collection $functions
     */
    protected function setFunctions(Collection $functions)
    {
        $this->functions = $functions;
    }

    /**
     * @return Collection
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * @param Collection $interfaces
     */
    protected function setInterfaces(Collection $interfaces)
    {
        $this->interfaces = $interfaces;
    }

    /**
     * @return Collection
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @param Collection $namespaces
     */
    protected function setNamespaces(Collection $namespaces)
    {
        $this->namespaces = $namespaces;
    }

    /**
     * @return Collection
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }

    /**
     * @param Collection $traits
     */
    protected function setTraits($traits)
    {
        $this->traits = $traits;
    }

    /**
     * @return Collection
     */
    public function getTraits()
    {
        return $this->traits;
    }
}
