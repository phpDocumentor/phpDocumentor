<?php
namespace phpDocumentor\Descriptor;

class NamespaceDescriptor extends DescriptorAbstract implements Interfaces\NamespaceInterface
{
    /** @var NamespaceDescriptor $parentNamespace */
    protected $parentNamespace;

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
     * Sets the parent namespace for this namespace.
     *
     * @param NamespaceDescriptor $parentNamespace
     *
     * @return void
     */
    public function setParentNamespace($parentNamespace)
    {
        $this->parentNamespace = $parentNamespace;
    }

    /**
     * Returns the parent namespace for this namespace.
     *
     * @return NamespaceDescriptor|null
     */
    public function getParentNamespace()
    {
        return $this->parentNamespace;
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

    /**
     * References to child Descriptors/objects should be assigned a null when the containing object is nulled.
     *
     * In this method should all references to objects be assigned the value null; this will clear the references
     * of child objects from other objects.
     *
     * For example:
     *
     *     A class should NULL its constants, properties and methods as they are contained WITHIN the class and become
     *     orphans if not nulled.
     *
     * @return void
     */
    public function clearReferences()
    {
        $this->getNamespaces()->clearReferences();
        $this->getConstants()->clearReferences();
        $this->getFunctions()->clearReferences();
        $this->getClasses()->clearReferences();
        $this->getInterfaces()->clearReferences();
        $this->getTraits()->clearReferences();
    }
}
