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

use InvalidArgumentException;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use Webmozart\Assert\Assert;
use function array_filter;

/**
 * Descriptor representing a constant
 *
 * @api
 * @package phpDocumentor\AST
 */
class ConstantDescriptor extends DescriptorAbstract implements
    Interfaces\ConstantInterface,
    Interfaces\VisibilityInterface
{
    /** @var ClassDescriptor|InterfaceDescriptor|null $parent */
    protected $parent;

    /** @var Type|null $types */
    protected $types;

    /** @var string $value */
    protected $value = '';

    /** @var string $visibility */
    protected $visibility = 'public';

    /**
     * Registers a parent class or interface with this constant.
     *
     * @param ClassDescriptor|InterfaceDescriptor|null $parent
     *
     * @throws InvalidArgumentException If anything other than a class, interface or null was passed.
     */
    public function setParent(?DescriptorAbstract $parent) : void
    {
        Assert::nullOrIsInstanceOfAny(
            $parent,
            [ClassDescriptor::class, InterfaceDescriptor::class],
            'Constants can only have an interface or class as parent'
        );

        $fqsen = $parent !== null
            ? $parent->getFullyQualifiedStructuralElementName() . '::' . $this->getName()
            : $this->getName();

        $this->setFullyQualifiedStructuralElementName(new Fqsen($fqsen));

        $this->parent = $parent;
    }

    /**
     * @return ClassDescriptor|InterfaceDescriptor|null
     */
    public function getParent() : ?DescriptorAbstract
    {
        return $this->parent;
    }

    public function setTypes(Type $types) : void
    {
        $this->types = $types;
    }

    /**
     * @return list<Type>
     */
    public function getTypes() : array
    {
        return array_filter([$this->getType()]);
    }

    public function getType() : ?Type
    {
        if ($this->types === null) {
            $var = $this->getVar()->fetch(0);
            if ($var instanceof VarDescriptor) {
                return $var->getType();
            }
        }

        return $this->types;
    }

    public function setValue(string $value) : void
    {
        $this->value = $value;
    }

    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * @return Collection<VarDescriptor>
     */
    public function getVar() : Collection
    {
        /** @var Collection<VarDescriptor> $var */
        $var = $this->getTags()->fetch('var', new Collection());
        if ($var->count() !== 0) {
            return $var;
        }

        $inheritedElement = $this->getInheritedElement();
        if ($inheritedElement) {
            return $inheritedElement->getVar();
        }

        return new Collection();
    }

    /**
     * Returns the file associated with the parent class, interface or trait when inside a container.
     */
    public function getFile() : FileDescriptor
    {
        return parent::getFile() ?: $this->getParent()->getFile();
    }

    /**
     * Returns the Constant from which this one should inherit, if any.
     */
    public function getInheritedElement() : ?ConstantDescriptor
    {
        /** @var ClassDescriptor|InterfaceDescriptor|null $associatedClass */
        $associatedClass = $this->getParent();

        if (($associatedClass instanceof ClassDescriptor || $associatedClass instanceof InterfaceDescriptor)
            && ($associatedClass->getParent() instanceof ClassDescriptor
                || $associatedClass->getParent() instanceof InterfaceDescriptor
            )
        ) {
            /** @var ClassDescriptor|InterfaceDescriptor $parentClass */
            $parentClass = $associatedClass->getParent();

            return $parentClass->getConstants()->fetch($this->getName());
        }

        return null;
    }

    public function setVisibility(string $visibility) : void
    {
        $this->visibility = $visibility;
    }

    public function getVisibility() : string
    {
        return $this->visibility;
    }
}
