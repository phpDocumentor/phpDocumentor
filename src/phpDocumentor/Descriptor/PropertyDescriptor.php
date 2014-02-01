<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
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
     * Returns the summary which describes this element.
     *
     * This method will automatically attempt to inherit the parent's summary if this one has none.
     *
     * @return string
     */
    public function getSummary()
    {
        if ($this->summary && strtolower(trim($this->summary)) != '{@inheritdoc}') {
            return parent::getSummary();
        }

        $parentConstant = $this->getInheritedElement();
        if ($parentConstant) {
            return $parentConstant->getSummary();
        }

        return '';
    }

    /**
     * Returns the description which describes this element.
     *
     * This method will automatically attempt to inherit the parent's description if this one has none.
     *
     * @return string
     */
    public function getDescription()
    {
        if ($this->description && strtolower(trim($this->description)) != '{@inheritdoc}') {
            return parent::getDescription();
        }

        $parentConstant = $this->getInheritedElement();
        if ($parentConstant) {
            return $parentConstant->getDescription();
        }

        return '';
    }

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
        /** @var Collection $var */
        $var = $this->getTags()->get('var', new Collection());

        if ($var->count() == 0
            && ($this->getParent() instanceof ChildInterface)
            && ($this->getParent()->getParent() instanceof ClassDescriptor)
        ) {
            /** @var PropertyDescriptor|null $parentProperty */
            $parentProperty = $this->getParent()->getParent()->getProperties()->get($this->getName());
            if ($parentProperty) {
                return $parentProperty->getVar();
            }
        }

        return $var;
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
    protected function getInheritedElement()
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
