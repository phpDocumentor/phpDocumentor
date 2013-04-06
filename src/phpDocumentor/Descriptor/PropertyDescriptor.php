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

/**
 * Descriptor representing a property.
 */
class PropertyDescriptor extends DescriptorAbstract implements Interfaces\PropertyInterface
{
    /** @var ClassDescriptor|TraitDescriptor $parent */
    protected $parent;

    /** @var string[] $types */
    protected $types = array();

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
}
