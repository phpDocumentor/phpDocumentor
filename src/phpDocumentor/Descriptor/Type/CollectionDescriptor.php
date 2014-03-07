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

    /** @var TypeInterface|null $type */
    protected $types;

    /** @var TypeInterface|null $type */
    protected $keyTypes;

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
}
