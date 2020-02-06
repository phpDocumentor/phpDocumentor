<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use InvalidArgumentException;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use Webmozart\Assert\Assert;

/**
 * Descriptor representing a constant
 */
class ConstantDescriptor extends DescriptorAbstract implements Interfaces\ConstantInterface
{
    /** @var ClassDescriptor|InterfaceDescriptor|null $parent */
    protected $parent;

    /** @var Type $types */
    protected $types;

    /** @var string $value */
    protected $value = '';

    /**
     * Registers a parent class or interface with this constant.
     *
     * @param ClassDescriptor|InterfaceDescriptor|null $parent
     *
     * @throws InvalidArgumentException If anything other than a class, interface or null was passed.
     */
    public function setParent($parent) : void
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
     * @return ClassDescriptor|InterfaceDescriptor|FileDescriptor|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setTypes(Type $types) : void
    {
        $this->types = $types;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes() : array
    {
        return [$this->getType()];
    }

    public function getType() : ?Type
    {
        if ($this->types === null) {
            $var = $this->getVar()->get(0);
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
        $var = $this->getTags()->get('var', new Collection());
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

        if ($associatedClass instanceof ClassDescriptor && $associatedClass->getParent() instanceof ClassDescriptor) {
            /** @var ClassDescriptor $parentClass */
            $parentClass = $associatedClass->getParent();

            return $parentClass->getConstants()->get($this->getName());
        }

        return null;
    }
}
