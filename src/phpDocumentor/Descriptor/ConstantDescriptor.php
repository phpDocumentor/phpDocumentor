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
     * @param null|ClassDescriptor|InterfaceDescriptor $parent
     */
    public function setParent($parent)
    {
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
            $var = current($this->getVar());
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
        /** @var Collection $version */
        $var = $this->getTags()->get('var', new Collection());

        if ($var->count() == 0 && ($this->getParent() instanceof ChildInterface)) {
            $parentConstant = $this->getParent()->getConstants()->get($this->getName());
            if ($parentConstant) {
                return $parentConstant->getVar();
            }
        }

        return $var;
    }
}
