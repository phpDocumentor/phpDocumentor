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

use phpDocumentor\Descriptor\Interfaces\ArgumentInterface;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\MethodInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Descriptor\Traits\CanBeAbstract;
use phpDocumentor\Descriptor\Traits\CanBeFinal;
use phpDocumentor\Descriptor\Traits\HasAttributes;
use phpDocumentor\Descriptor\Traits\HasVisibility;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use Webmozart\Assert\Assert;

use function current;

/**
 * Descriptor representing a Method in a Class, Interface or Trait.
 *
 * @api
 * @package phpDocumentor\AST
 */
class MethodDescriptor extends DescriptorAbstract implements
    Interfaces\MethodInterface,
    Interfaces\VisibilityInterface
{
    use CanBeFinal;
    use CanBeAbstract;
    use HasVisibility;
    use HasAttributes;

    /** @var ClassInterface|InterfaceInterface|TraitInterface|EnumInterface|null $parent */
    protected ElementInterface|null $parent = null;

    /** @var Collection<ArgumentInterface> */
    protected Collection $arguments;

    protected bool $static = false;
    private Type|null $returnType = null;
    private bool $hasReturnByReference = false;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setArguments(new Collection());
    }

    /**
     * {@inheritDoc}
     */
    public function setParent($parent): void
    {
        /** @var ClassInterface|InterfaceInterface|TraitInterface|EnumInterface $parent */
        Assert::isInstanceOfAny(
            $parent,
            [ClassInterface::class, TraitInterface::class, InterfaceInterface::class, EnumInterface::class],
        );

        $this->setFullyQualifiedStructuralElementName(
            new Fqsen($parent->getFullyQualifiedStructuralElementName() . '::' . $this->getName() . '()'),
        );

        // reset cached inherited element so that it can be re-detected.
        $this->inheritedElement = null;

        $this->parent = $parent;
    }

    /** @return ClassInterface|InterfaceInterface|TraitInterface|EnumInterface|null */
    public function getParent(): ElementInterface|null
    {
        return $this->parent;
    }

    public function setStatic(bool $static): void
    {
        $this->static = $static;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }

    /** @param Collection<ArgumentInterface> $arguments */
    public function setArguments(Collection $arguments): void
    {
        $this->arguments = Collection::fromInterfaceString(ArgumentInterface::class);

        foreach ($arguments as $argument) {
            $this->addArgument($argument->getName(), $argument);
        }
    }

    public function addArgument(string $name, ArgumentInterface $argument): void
    {
        $argument->setMethod($this);
        $this->arguments->set($name, $argument);
    }

    /** @return Collection<ArgumentInterface> */
    public function getArguments(): Collection
    {
        return $this->arguments;
    }

    public function getResponse(): ReturnDescriptor
    {
        $definedReturn = new ReturnDescriptor('return');
        $definedReturn->setType($this->returnType);

        /** @var Collection<ReturnDescriptor>|null $returnTags */
        $returnTags = $this->getReturn();

        if ($returnTags instanceof Collection && $returnTags->count() > 0) {
            return current($returnTags->getAll());
        }

        return $definedReturn;
    }

    /**
     * Returns the file associated with the parent class, interface or trait.
     */
    public function getFile(): FileInterface
    {
        $file = $this->getParent()->getFile();

        Assert::notNull($file);

        return $file;
    }

    /** @return Collection<ReturnDescriptor> */
    public function getReturn(): Collection
    {
        /** @var Collection<ReturnDescriptor> $var */
        $var = $this->getTags()->fetch('return', new Collection())->filter(ReturnDescriptor::class);
        if ($var->count() !== 0) {
            return $var;
        }

        $inheritedElement = $this->getInheritedElement();
        if ($inheritedElement) {
            return $inheritedElement->getReturn();
        }

        return new Collection();
    }

    /** @return Collection<ParamDescriptor> */
    public function getParam(): Collection
    {
        /** @var Collection<ParamDescriptor> $var */
        $var = $this->getTags()->fetch('param', new Collection());
        if ($var instanceof Collection && $var->count() > 0) {
            return $var;
        }

        $inheritedElement = $this->getInheritedElement();
        if ($inheritedElement) {
            return $inheritedElement->getParam();
        }

        return new Collection();
    }

    /**
     * Returns the Method from which this method should inherit its information, if any.
     *
     * The inheritance scheme for a method is more complicated than for most elements; the following business rules
     * apply:
     *
     * 1. if the parent class/interface extends another class or other interfaces (interfaces have multiple
     *    inheritance!) then:
     *    1. Check each parent class/interface's parent if they have a method with the exact same name
     *    2. if a method is found with the same name; return the first one encountered.
     * 2. if the parent is a class and implements interfaces, check each interface for a method with the exact same
     *    name. If such a method is found, return the first hit.
     */
    public function getInheritedElement(): MethodInterface|null
    {
        if ($this->inheritedElement !== null) {
            Assert::isInstanceOf($this->inheritedElement, self::class);

            return $this->inheritedElement;
        }

        /** @var ClassInterface|InterfaceInterface|null $methodParent */
        $methodParent = $this->getParent();
        if ($methodParent instanceof ClassInterface) {
            /** @var MethodInterface|null $parentClassMethod */
            $parentClassMethod = $this->recurseClassInheritance($methodParent);
            if ($parentClassMethod instanceof self) {
                $this->inheritedElement = $parentClassMethod;
            }
        } elseif ($methodParent instanceof InterfaceInterface) {
            /** @var MethodInterface|null $parentInterfaceMethod */
            $parentInterfaceMethod = $this->recurseInterfaceInheritance($methodParent);
            if ($parentInterfaceMethod instanceof self) {
                $this->inheritedElement = $parentInterfaceMethod;
            }
        }

        return $this->inheritedElement;
    }

    /**
     * Sets return type of this method.
     */
    public function setReturnType(Type $returnType): void
    {
        $this->returnType = $returnType;
    }

    public function setHasReturnByReference(bool $hasReturnByReference): void
    {
        $this->hasReturnByReference = $hasReturnByReference;
    }

    public function getHasReturnByReference(): bool
    {
        return $this->hasReturnByReference;
    }

    private function recurseClassInheritance(ClassInterface $currentClass): MethodInterface|null
    {
        /** @var ClassInterface|null $parentClass */
        $parentClass = $currentClass->getParent();
        if ($parentClass instanceof ClassInterface) {
            /** @var MethodInterface|null $parentClassMethod */
            $parentClassMethod = $parentClass->getMethods()->fetch($this->getName());
            if ($parentClassMethod instanceof self) {
                return $parentClassMethod;
            }

            /** @var MethodInterface|null $ancestorMethod */
            $ancestorMethod = $this->recurseClassInheritance($parentClass);
            if ($ancestorMethod instanceof self) {
                return $ancestorMethod;
            }
        }

        /** @var Collection<InterfaceInterface>|null $parentInterfaces */
        $parentInterfaces = $currentClass->getInterfaces()->filter(InterfaceInterface::class);
        foreach ($parentInterfaces as $parentInterface) {
            /** @var MethodInterface|null $parentInterfaceMethod */
            $parentInterfaceMethod = $parentInterface->getMethods()->fetch($this->getName());
            if ($parentInterfaceMethod instanceof self) {
                return $parentInterfaceMethod;
            }

            /** @var MethodInterface|null $ancestorMethod */
            $ancestorMethod = $this->recurseInterfaceInheritance($parentInterface);
            if ($ancestorMethod instanceof self) {
                return $ancestorMethod;
            }
        }

        return null;
    }

    private function recurseInterfaceInheritance(InterfaceInterface $currentInterface): MethodInterface|null
    {
        /** @var Collection<InterfaceInterface>|null $parentInterfaces */
        $parentInterfaces = $currentInterface->getParent()->filter(InterfaceInterface::class);
        foreach ($parentInterfaces as $parentInterface) {
            /** @var MethodInterface|null $parentInterfaceMethod */
            $parentInterfaceMethod = $parentInterface->getMethods()->fetch($this->getName());
            if ($parentInterfaceMethod instanceof self) {
                return $parentInterfaceMethod;
            }

            /** @var MethodInterface|null $ancestorMethod */
            $ancestorMethod = $this->recurseInterfaceInheritance($parentInterface);
            if ($ancestorMethod instanceof self) {
                return $ancestorMethod;
            }
        }

        return null;
    }
}
