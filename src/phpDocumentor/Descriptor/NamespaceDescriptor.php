<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

/**
 * Represents a namespace and its children for a project.
 */
class NamespaceDescriptor extends DescriptorAbstract implements Interfaces\NamespaceInterface
{
    /** @var NamespaceDescriptor $parentNamespace */
    protected $parent;

    /** @var Collection $namespaces*/
    protected $children;

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

    /**
     * Initializes the namespace with collections for its children.
     */
    public function __construct()
    {
        $this->setChildren(new Collection());
        $this->setFunctions(new Collection());
        $this->setConstants(new Collection());
        $this->setClasses(new Collection());
        $this->setInterfaces(new Collection());
        $this->setTraits(new Collection());
        $this->setTags(new Collection());
    }

    /**
     * Sets the parent namespace for this namespace.
     *
     * @param NamespaceDescriptor $parent
     *
     * @return void
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns the parent namespace for this namespace.
     *
     * @return NamespaceDescriptor|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets a list of all classes in this project.
     *
     * @param Collection $classes
     *
     * @return void
     */
    public function setClasses(Collection $classes)
    {
        $this->classes = $classes;
    }

    /**
     * Returns a list of all classes in this namespace.
     *
     * @return Collection
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * Sets a list of all constants in this namespace.
     *
     * @param Collection $constants
     *
     * @return void
     */
    public function setConstants(Collection $constants)
    {
        $this->constants = $constants;
    }

    /**
     * Returns a list of all constants in this namespace.
     *
     * @return Collection
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * Sets a list of all functions in this namespace.
     *
     * @param Collection $functions
     *
     * @return void
     */
    public function setFunctions(Collection $functions)
    {
        $this->functions = $functions;
    }

    /**
     * Returns a list of all functions in this namespace.
     *
     * @return Collection
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Sets a list of all interfaces in this namespace.
     *
     * @param Collection $interfaces
     *
     * @return void
     */
    public function setInterfaces(Collection $interfaces)
    {
        $this->interfaces = $interfaces;
    }

    /**
     * Returns a list of all interfaces in this namespace.
     *
     * @return Collection
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * Sets a list of all child namespaces in this namespace.
     *
     * @param Collection $children
     *
     * @return void
     */
    public function setChildren(Collection $children)
    {
        $this->children = $children;
    }

    /**
     * Returns a list of all namespaces contained in this namespace and its children.
     *
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Sets a list of all traits contained in this namespace.
     *
     * @param Collection $traits
     *
     * @return void
     */
    public function setTraits($traits)
    {
        $this->traits = $traits;
    }

    /**
     * Returns a list of all traits in this namespace.
     *
     * @return Collection
     */
    public function getTraits()
    {
        return $this->traits;
    }
}
