<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

/**
 * Represents a namespace and its children for a project.
 *
 * @api
 * @package phpDocumentor\AST
 */
class NamespaceDescriptor extends DescriptorAbstract implements Interfaces\NamespaceInterface
{
    /** @var NamespaceDescriptor|null $parent */
    protected $parent;

    /** @var Collection<NamespaceDescriptor> $children */
    protected $children;

    /** @var Collection<FunctionDescriptor> $functions */
    protected $functions;

    /** @var Collection<ConstantDescriptor> $constants */
    protected $constants;

    /** @var Collection<ClassDescriptor> $classes */
    protected $classes;

    /** @var Collection<InterfaceDescriptor> $interfaces */
    protected $interfaces;

    /** @var Collection<TraitDescriptor> $traits */
    protected $traits;

    /**
     * Initializes the namespace with collections for its children.
     */
    public function __construct()
    {
        parent::__construct();
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
     * @param ?NamespaceDescriptor $parent
     */
    public function setParent($parent) : void
    {
        $this->parent = $parent;
    }

    /**
     * Returns the parent namespace for this namespace.
     */
    public function getParent() : ?NamespaceDescriptor
    {
        return $this->parent;
    }

    /**
     * Sets a list of all classes in this project.
     *
     * @param Collection<ClassDescriptor> $classes
     */
    public function setClasses(Collection $classes) : void
    {
        $this->classes = $classes;
    }

    /**
     * Returns a list of all classes in this namespace.
     *
     * @return Collection<ClassDescriptor>
     */
    public function getClasses() : Collection
    {
        return $this->classes;
    }

    /**
     * Sets a list of all constants in this namespace.
     *
     * @param Collection<ConstantDescriptor> $constants
     */
    public function setConstants(Collection $constants) : void
    {
        $this->constants = $constants;
    }

    /**
     * Returns a list of all constants in this namespace.
     *
     * @return Collection<ConstantDescriptor>
     */
    public function getConstants() : Collection
    {
        return $this->constants;
    }

    /**
     * Sets a list of all functions in this namespace.
     *
     * @param Collection<FunctionDescriptor> $functions
     */
    public function setFunctions(Collection $functions) : void
    {
        $this->functions = $functions;
    }

    /**
     * Returns a list of all functions in this namespace.
     *
     * @return Collection<FunctionDescriptor>
     */
    public function getFunctions() : Collection
    {
        return $this->functions;
    }

    /**
     * Sets a list of all interfaces in this namespace.
     *
     * @param Collection<InterfaceDescriptor> $interfaces
     */
    public function setInterfaces(Collection $interfaces) : void
    {
        $this->interfaces = $interfaces;
    }

    /**
     * Returns a list of all interfaces in this namespace.
     *
     * @return Collection<InterfaceDescriptor>
     */
    public function getInterfaces() : Collection
    {
        return $this->interfaces;
    }

    /**
     * Sets a list of all child namespaces in this namespace.
     *
     * @param Collection<NamespaceDescriptor> $children
     */
    public function setChildren(Collection $children) : void
    {
        $this->children = $children;
    }

    /**
     * Returns a list of all namespaces contained in this namespace and its children.
     *
     * @return Collection<NamespaceDescriptor>
     */
    public function getChildren() : Collection
    {
        return $this->children;
    }

    /**
     * Sets a list of all traits contained in this namespace.
     *
     * @param Collection<TraitDescriptor> $traits
     */
    public function setTraits(Collection $traits) : void
    {
        $this->traits = $traits;
    }

    /**
     * Returns a list of all traits in this namespace.
     *
     * @return Collection<TraitDescriptor>
     */
    public function getTraits() : Collection
    {
        return $this->traits;
    }
}
