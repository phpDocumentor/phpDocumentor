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

namespace phpDocumentor\Descriptor\Type;

use phpDocumentor\Descriptor\Interfaces\TypeInterface;

/**
 * Descriptor representing a collection
 */
class CollectionDescriptor implements TypeInterface
{
    /** @var TypeInterface|string */
    protected $baseType = '';

    /** @var TypeInterface[] $type */
    protected $types = array();

    /** @var TypeInterface[] $type */
    protected $keyTypes = array();

    public function __construct($baseType)
    {
        $this->baseType = $baseType;
    }

    /**
     * Returns the name for this type.
     *
     * @return TypeInterface
     */
    public function getName()
    {
        return $this->baseType instanceof TypeInterface ? $this->baseType->getName() : $this->baseType;
    }

    /**
     * Returns the base type for this Collection or null if there is no attached type.
     *
     * When the presented collection is governed by an object (such as a Collection object) then a reference to that
     * object will be returned. If however the base type for this collection is a simple type such as an 'array' then
     * we return null to indicate there is no object governing this type.
     *
     * @return TypeInterface|null
     */
    public function getBaseType()
    {
        return $this->baseType instanceof TypeInterface ? $this->baseType : null;
    }

    public function setBaseType($baseType)
    {
        $this->baseType = $baseType;
    }

    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function setKeyTypes(array $types)
    {
        $this->keyTypes = $types;
    }

    public function getKeyTypes()
    {
        return $this->keyTypes;
    }

    public function __toString()
    {
        $name = $this->getName();
        $types = array();
        foreach ($this->getTypes() as $key => $type) {
            $types[] = (string)$type;
        }

        if (count($types) > 0) {
            $name .= '<' . implode(',', $types) . '>';
        }

        return $name;
    }
}
