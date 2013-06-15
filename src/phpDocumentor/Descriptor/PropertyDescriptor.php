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

use phpDocumentor\Descriptor\Interfaces\ChildInterface;
use phpDocumentor\Descriptor\Tag\VarDescriptor;

/**
 * Descriptor representing a property.
 */
class PropertyDescriptor extends DescriptorAbstract implements Interfaces\PropertyInterface
{
    /** @var ClassDescriptor|TraitDescriptor $parent */
    protected $parent;

    /** @var string[]|null $types */
    protected $types;

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
            $parent->getFullyQualifiedStructuralElementName() . '::' . $this->getName()
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
    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes()
    {
        if (!$this->types) {
            $this->types = array();

            /** @var VarDescriptor $var */
            $var = $this->getVar()->getIterator()->current();
            if ($var) {
                $this->types = $var->getTypes();
            }
        }

        return $this->types;
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
        /** @var Collection $version */
        $var = $this->getTags()->get('var', new Collection());

        if ($var->count() == 0
            && ($this->getParent() instanceof ChildInterface)
            && (
                $this->getParent()->getParent() instanceof ClassDescriptor
                || $this->getParent()->getParent() instanceof InterfaceDescriptor
            )
        ) {
            /** @var PropertyDescriptor|null $parentProperty */
            $parentProperty = $this->getParent()->getParent()->getProperties()->get($this->getName());
            if ($parentProperty) {
                return $parentProperty->getVar();
            }
        }

        return $var;
    }
}
