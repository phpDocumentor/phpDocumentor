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

use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ConstantInterface;
use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Descriptor\Interfaces\FunctionInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use Webmozart\Assert\Assert;

/**
 * Represents a namespace and its children for a project.
 *
 * @api
 * @package phpDocumentor\AST
 */
class NamespaceDescriptor extends DescriptorAbstract implements Interfaces\NamespaceInterface
{
    protected NamespaceInterface|null $parent = null;

    /** @var Collection<NamespaceInterface> $children */
    protected Collection $children;

    /** @var Collection<FunctionInterface> $functions */
    protected Collection $functions;

    /** @var Collection<ConstantInterface> $constants */
    protected Collection $constants;

    /** @var Collection<ClassInterface> $classes */
    protected Collection $classes;

    /** @var Collection<InterfaceInterface> $interfaces */
    protected Collection $interfaces;

    /** @var Collection<TraitInterface> $traits */
    protected Collection $traits;

    /** @var Collection<EnumInterface> */
    private Collection $enums;

    /**
     * Initializes the namespace with collections for its children.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setChildren(Collection::fromInterfaceString(NamespaceInterface::class));
        $this->setFunctions(Collection::fromInterfaceString(FunctionInterface::class));
        $this->setConstants(Collection::fromInterfaceString(ConstantInterface::class));
        $this->setClasses(Collection::fromInterfaceString(ClassInterface::class));
        $this->setInterfaces(Collection::fromInterfaceString(InterfaceInterface::class));
        $this->setTraits(Collection::fromInterfaceString(TraitInterface::class));
        $this->setTags(new Collection());
        $this->setEnums(Collection::fromInterfaceString(EnumInterface::class));
    }

    /**
     * {@inheritDoc}
     */
    public function setParent($parent): void
    {
        //phpcs:disable SlevomatCodingStandard.Classes.ModernClassNameReference.ClassNameReferencedViaMagicConstant
        Assert::nullOrIsInstanceOf($parent, self::class);

        $this->parent = $parent;
    }

    /**
     * Returns the parent namespace for this namespace.
     */
    public function getParent(): NamespaceInterface|null
    {
        return $this->parent;
    }

    /**
     * Sets a list of all classes in this project.
     *
     * @param Collection<ClassInterface> $classes
     */
    public function setClasses(Collection $classes): void
    {
        $this->classes = $classes;
    }

    /**
     * Returns a list of all classes in this namespace.
     *
     * @return Collection<ClassInterface>
     */
    public function getClasses(): Collection
    {
        return $this->classes;
    }

    /**
     * Sets a list of all constants in this namespace.
     *
     * @param Collection<ConstantInterface> $constants
     */
    public function setConstants(Collection $constants): void
    {
        $this->constants = $constants;
    }

    /**
     * Returns a list of all constants in this namespace.
     *
     * @return Collection<ConstantInterface>
     */
    public function getConstants(): Collection
    {
        return $this->constants;
    }

    /**
     * Sets a list of all functions in this namespace.
     *
     * @param Collection<FunctionInterface> $functions
     */
    public function setFunctions(Collection $functions): void
    {
        $this->functions = $functions;
    }

    /**
     * Returns a list of all functions in this namespace.
     *
     * @return Collection<FunctionInterface>
     */
    public function getFunctions(): Collection
    {
        return $this->functions;
    }

    /**
     * Sets a list of all interfaces in this namespace.
     *
     * @param Collection<InterfaceInterface> $interfaces
     */
    public function setInterfaces(Collection $interfaces): void
    {
        $this->interfaces = $interfaces;
    }

    /**
     * Returns a list of all interfaces in this namespace.
     *
     * @return Collection<InterfaceInterface>
     */
    public function getInterfaces(): Collection
    {
        return $this->interfaces;
    }

    public function addChild(NamespaceInterface $namespaceDescriptor): void
    {
        $this->children->set($namespaceDescriptor->getName(), $namespaceDescriptor);
        $namespaceDescriptor->setParent($this);
    }

    /**
     * Sets a list of all child namespaces in this namespace.
     *
     * @param Collection<NamespaceInterface> $children
     */
    public function setChildren(Collection $children): void
    {
        $this->children = $children;
    }

    /**
     * Returns a list of all namespaces contained in this namespace and its children.
     *
     * @return Collection<NamespaceInterface>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * Sets a list of all traits contained in this namespace.
     *
     * @param Collection<TraitInterface> $traits
     */
    public function setTraits(Collection $traits): void
    {
        $this->traits = $traits;
    }

    /**
     * Returns a list of all traits in this namespace.
     *
     * @return Collection<TraitInterface>
     */
    public function getTraits(): Collection
    {
        return $this->traits;
    }

    /**
     * Returns a list of all enums in this namespace.
     *
     * @return Collection<EnumInterface>
     */
    public function getEnums(): Collection
    {
        return $this->enums;
    }

    /**
     * Sets a list of all enums contained in this namespace.
     *
     * @param Collection<EnumInterface> $enums
     */
    public function setEnums(Collection $enums): void
    {
        $this->enums = $enums;
    }

    /**
     * Returns true when the namespace is empty.
     */
    public function isEmpty(): bool
    {
        return $this->classes->count() === 0
            && $this->constants->count() === 0
            && $this->functions->count() === 0
            && $this->interfaces->count() === 0
            && $this->traits->count() === 0
            && $this->enums->count() === 0
            && $this->children->count() === 0;
    }
}
