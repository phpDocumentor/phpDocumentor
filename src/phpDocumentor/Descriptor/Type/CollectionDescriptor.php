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
 * Descriptor representing a collection or compound type of collection object.
 *
 * This descriptor represents any type that is capable of containing other typed values. Examples of such
 * types can be an array, DoctrineCollection or ArrayObject.
 */
class CollectionDescriptor implements TypeInterface
{
    /** @var TypeInterface|string */
    protected $baseType = '';

    /** @var TypeInterface[] $type */
    protected $types = array();

    /** @var TypeInterface[] $type */
    protected $keyTypes = array();

    /**
     * Initializes this type collection with its base-type.
     *
     * @param TypeInterface $baseType
     */
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

    /**
     * Registers the base type for this collection type.
     *
     * @param string|TypeInterface $baseType
     *
     * @return void
     */
    public function setBaseType($baseType)
    {
        $this->baseType = $baseType;
    }

    /**
     * Register the type, or set of types, to which a value in this type of collection can belong.
     *
     * @param TypeInterface[] $types
     *
     * @return void
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    /**
     * Returns the type, or set of types, to which a value in this type of collection can belong.
     *
     * @return TypeInterface[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Registers the type, or set of types, to which a *key* in this type of collection can belong.
     *
     * @param TypeInterface[] $types
     *
     * @return void
     */
    public function setKeyTypes(array $types)
    {
        $this->keyTypes = $types;
    }

    /**
     * Registers the type, or set of types, to which a *key* in this type of collection can belong.
     *
     * @return TypeInterface[]
     */
    public function getKeyTypes()
    {
        return $this->keyTypes;
    }

    /**
     * Returns a human-readable representation for this type.
     *
     * @return string
     */
    public function __toString()
    {
        $name = $this->getName();

        $keyTypes = array();
        foreach ($this->getKeyTypes() as $type) {
            $keyTypes[] = (string) $type;
        }

        $types = array();
        foreach ($this->getTypes() as $type) {
            $types[] = (string) $type;
        }

        if (count($types) > 0) {
            $name .= '<' . ($keyTypes ? implode('|', $keyTypes) . ',' : '') . implode('|', $types) . '>';
        }

        return $name;
    }
}
