<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Tag\VarDescriptor;
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
    public function setParent($parent)
    {
        $this->setFullyQualifiedStructuralElementName(
            $parent->getFullyQualifiedStructuralElementName() . '::$' . $this->getName()
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

    /**
     * {@inheritDoc}
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * {@inheritDoc}
     */
    public function setStatic($static)
    {
        $this->static = $static;
    }

    /**
     * {@inheritDoc}
     */
    public function isStatic()
    {
        return $this->static;
    }

    /**
     * {@inheritDoc}
     */
    public function setType(Type $type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes()
    {
        if ($this->getType() === null) {
            return [];
        }

        return [(string)$this->getType()];
    }

    public function getType()
    {
        if ($this->type === null) {
            /** @var VarDescriptor $var */
            $var = $this->getVar()->getIterator()->current();
            if ($var) {
                return $var->getType();
            }
        }

        return $this->type;
    }

    /**
     * {@inheritDoc}
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * {@inheritDoc}
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @return Collection
     */
    public function getVar()
    {
        /** @var Collection $var */
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
     *
     * @return FileDescriptor
     */
    public function getFile()
    {
        return $this->getParent()->getFile();
    }

    /**
     * Returns the property from which this one should inherit, if any.
     *
     * @return PropertyDescriptor|null
     */
    public function getInheritedElement()
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
