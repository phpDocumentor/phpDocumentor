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
 * Descriptor representing a constant
 */
class ConstantDescriptor extends DescriptorAbstract implements Interfaces\ConstantInterface
{
    /** @var ClassDescriptor|InterfaceDescriptor|null $parent */
    protected $parent;

    /** @var string[]|null $type */
    protected $types;

    /** @var string $value */
    protected $value;

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
     * Registers a parent class or interface with this constant.
     *
     * @param ClassDescriptor|InterfaceDescriptor|null $parent
     *
     * @throws \InvalidArgumentException if anything other than a class, interface or null was passed.
     *
     * @return void
     */
    public function setParent($parent)
    {
        if (!$parent instanceof ClassDescriptor && !$parent instanceof InterfaceDescriptor && $parent !== null) {
            throw new \InvalidArgumentException('Constants can only have an interface or class as parent');
        }

        $this->setFullyQualifiedStructuralElementName(
            $parent->getFullyQualifiedStructuralElementName() . '::' . $this->getName()
        );

        $this->parent = $parent;
    }

    /**
     * @return null|ClassDescriptor|InterfaceDescriptor
     */
    public function getParent()
    {
        return $this->parent;
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
        if ($this->types === null) {
            $this->types = array();

            /** @var VarDescriptor $var */
            $var = $this->getVar()->get(0);
            if ($var) {
                $this->types = $var->getTypes();
            }
        }

        return $this->types;
    }

    /**
     * {@inheritDoc}
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return Collection
     */
    public function getVar()
    {
        /** @var Collection $var */
        $var = $this->getTags()->get('var', new Collection());
        if ($var->count() != 0) {
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
     *
     * @return FileDescriptor
     */
    public function getFile()
    {
        return parent::getFile() ?: $this->getParent()->getFile();
    }

    /**
     * Returns the Constant from which this one should inherit, if any.
     *
     * @return ConstantDescriptor|null
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
            return $parentClass->getConstants()->get($this->getName());
        }

        return null;
    }
}
