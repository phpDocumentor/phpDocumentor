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

use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;

/**
 * Descriptor representing a property.
 */
class PropertyDescriptor extends DescriptorAbstract implements
    Interfaces\PropertyInterface,
    Interfaces\VisibilityInterface
{
    /** @var ClassDescriptor|TraitDescriptor $parent */
    protected $parent;

    /** @var Type $type */
    protected $type;

    /** @var string $default */
    protected $default;

    /** @var bool $static */
    protected $static = false;

    /** @var string $visibility */
    protected $visibility = 'public';

    /**
     * @param ClassDescriptor|TraitDescriptor $parent
     */
    public function setParent($parent) : void
    {
        $this->setFullyQualifiedStructuralElementName(
            new Fqsen($parent->getFullyQualifiedStructuralElementName() . '::$' . $this->getName())
        );

        $this->parent = $parent;
    }

    /**
     * @return ClassDescriptor|TraitDescriptor
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setDefault(?string $default) : void
    {
        $this->default = $default;
    }

    public function getDefault() : ?string
    {
        return $this->default;
    }

    public function setStatic(bool $static) : void
    {
        $this->static = $static;
    }

    public function isStatic() : bool
    {
        return $this->static;
    }

    public function setType(Type $type) : void
    {
        $this->type = $type;
    }

    /**
     * @return list<string>
     */
    public function getTypes() : array
    {
        if ($this->getType() instanceof Type) {
            return [(string) $this->getType()];
        }

        return [];
    }

    public function getType() : Type
    {
        if ($this->type === null) {
            /** @var VarDescriptor|bool $var */
            $var = $this->getVar()->getIterator()->current();
            if ($var instanceof VarDescriptor) {
                return $var->getType();
            }
        }

        return $this->type;
    }

    public function setVisibility(string $visibility) : void
    {
        $this->visibility = $visibility;
    }

    public function getVisibility() : string
    {
        return $this->visibility;
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
     * Returns the file associated with the parent class or trait.
     */
    public function getFile() : FileDescriptor
    {
        return $this->getParent()->getFile();
    }

    /**
     * Returns the property from which this one should inherit, if any.
     */
    public function getInheritedElement() : ?PropertyDescriptor
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

            return $parentClass->getProperties()->get($this->getName());
        }

        return null;
    }
}
