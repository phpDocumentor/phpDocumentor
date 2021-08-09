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

use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
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
class MethodDescriptor extends DescriptorAbstract implements Interfaces\MethodInterface, Interfaces\VisibilityInterface
{
    /** @var ClassDescriptor|InterfaceDescriptor|TraitDescriptor|null $parent */
    protected $parent;

    /** @var bool $abstract */
    protected $abstract = false;

    /** @var bool $final */
    protected $final = false;

    /** @var bool $static */
    protected $static = false;

    /** @var string $visibility */
    protected $visibility = 'public';

    /** @var Collection<ArgumentDescriptor> */
    protected $arguments;

    /** @var Type */
    private $returnType;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setArguments(new Collection());
    }

    /**
     * @param ClassDescriptor|InterfaceDescriptor|TraitDescriptor $parent
     */
    public function setParent(DescriptorAbstract $parent): void
    {
        $this->setFullyQualifiedStructuralElementName(
            new Fqsen($parent->getFullyQualifiedStructuralElementName() . '::' . $this->getName() . '()')
        );

        // reset cached inherited element so that it can be re-detected.
        $this->inheritedElement = null;

        $this->parent = $parent;
    }

    /**
     * @return ClassDescriptor|InterfaceDescriptor|TraitDescriptor|null
     */
    public function getParent(): ?DescriptorAbstract
    {
        return $this->parent;
    }

    public function setAbstract(bool $abstract): void
    {
        $this->abstract = $abstract;
    }

    public function isAbstract(): bool
    {
        return $this->abstract;
    }

    public function setFinal(bool $final): void
    {
        $this->final = $final;
    }

    public function isFinal(): bool
    {
        return $this->final;
    }

    public function setStatic(bool $static): void
    {
        $this->static = $static;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }

    public function setVisibility(string $visibility): void
    {
        $this->visibility = $visibility;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * @param Collection<ArgumentDescriptor> $arguments
     */
    public function setArguments(Collection $arguments): void
    {
        $this->arguments = Collection::fromClassString(ArgumentDescriptor::class);

        foreach ($arguments as $argument) {
            $this->addArgument($argument->getName(), $argument);
        }
    }

    public function addArgument(string $name, ArgumentDescriptor $argument): void
    {
        $argument->setMethod($this);
        $this->arguments->set($name, $argument);
    }

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
    public function getFile(): FileDescriptor
    {
        $file = $this->getParent()->getFile();

        Assert::notNull($file);

        return $file;
    }

    /**
     * @return Collection<ReturnDescriptor>
     */
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

    /**
     * @return Collection<ParamDescriptor>
     */
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
    public function getInheritedElement(): ?MethodDescriptor
    {
        if ($this->inheritedElement !== null) {
            Assert::isInstanceOf($this->inheritedElement, self::class);

            return $this->inheritedElement;
        }

        /** @var ClassDescriptor|InterfaceDescriptor|null $associatedClass */
        $associatedClass = $this->getParent();
        if (!$associatedClass instanceof ClassDescriptor && !$associatedClass instanceof InterfaceDescriptor) {
            return null;
        }

        $parentClass = $associatedClass->getParent();
        if ($parentClass instanceof ClassDescriptor || $parentClass instanceof Collection) {
            // the parent of a class is always a class, but the parent of an interface is a collection of interfaces.
            $parents = $parentClass instanceof ClassDescriptor ? [$parentClass] :
                $parentClass->filter(InterfaceDescriptor::class);
            foreach ($parents as $parent) {
                /** @var MethodDescriptor|null $parentMethod */
                $parentMethod = $parent->getMethods()->fetch($this->getName());
                if ($parentMethod instanceof self) {
                    $this->inheritedElement = $parentMethod;

                    return $this->inheritedElement;
                }
            }
        }

        // also check all implemented interfaces next if the parent is a class and not an interface
        if ($associatedClass instanceof ClassDescriptor) {
            /** @var InterfaceDescriptor|Fqsen $interface */
            foreach ($associatedClass->getInterfaces() as $interface) {
                if ($interface instanceof Fqsen) {
                    continue;
                }

                /** @var ?MethodDescriptor $parentMethod */
                $parentMethod = $interface->getMethods()->fetch($this->getName());
                if ($parentMethod instanceof self) {
                    $this->inheritedElement = $parentMethod;

                    return $this->inheritedElement;
                }
            }
        }

        return null;
    }

    /**
     * Sets return type of this method.
     */
    public function setReturnType(Type $returnType): void
    {
        $this->returnType = $returnType;
    }
}
