<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

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

    public function __construct()
    {
        $this->setChildren(new Collection());
        $this->setFunctions(new Collection());
        $this->setConstants(new Collection());
        $this->setClasses(new Collection());
        $this->setInterfaces(new Collection());
        $this->setTraits(new Collection());
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
     * @param Collection $classes
     */
    public function setClasses(Collection $classes)
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
    public function setConstants(Collection $constants)
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
    public function setFunctions(Collection $functions)
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
    public function setInterfaces(Collection $interfaces)
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
     * @param Collection $children
     */
    public function setChildren(Collection $children)
    {
        $this->children = $children;
    }

    /**
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Collection $traits
     */
    public function setTraits($traits)
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
