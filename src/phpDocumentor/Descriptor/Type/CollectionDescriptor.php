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

namespace phpDocumentor\Descriptor\Type;

use phpDocumentor\Descriptor\Interfaces\TypeInterface;
use function count;
use function implode;
use function method_exists;

/**
 * Descriptor representing a collection or compound type of collection object.
 *
 * This descriptor represents any type that is capable of containing other typed values. Examples of such
 * types can be an array, DoctrineCollection or ArrayObject.
 */
final class CollectionDescriptor implements TypeInterface
{
    /** @var TypeInterface|string */
    private $baseType;

    /** @var TypeInterface[] $type */
    private $types = [];

    /** @var TypeInterface[] $type */
    private $keyTypes = [];

    /**
     * Initializes this type collection with its base-type.
     */
    public function __construct(?TypeInterface $baseType = null)
    {
        $this->baseType = $baseType;
    }

    /**
     * Returns the name for this type.
     */
    public function getName() : string
    {
        return method_exists($this->baseType, 'getName')
            ? $this->baseType->getName()
            : ($this->baseType ?: 'array');
    }

    /**
     * Returns the base type for this Collection or null if there is no attached type.
     *
     * When the presented collection is governed by an object (such as a Collection object) then a reference to that
     * object will be returned. If however the base type for this collection is a simple type such as an 'array' then
     * we return null to indicate there is no object governing this type.
     */
    public function getBaseType() : ?TypeInterface
    {
        return $this->baseType instanceof TypeInterface ? $this->baseType : null;
    }

    /**
     * Registers the base type for this collection type.
     */
    public function setBaseType(?TypeInterface $baseType) : void
    {
        $this->baseType = $baseType;
    }

    /**
     * Register the type, or set of types, to which a value in this type of collection can belong.
     *
     * @param TypeInterface[] $types
     */
    public function setTypes(array $types) : void
    {
        $this->types = $types;
    }

    /**
     * Returns the type, or set of types, to which a value in this type of collection can belong.
     *
     * @return TypeInterface[]
     */
    public function getTypes() : array
    {
        return $this->types;
    }

    /**
     * Registers the type, or set of types, to which a *key* in this type of collection can belong.
     *
     * @param TypeInterface[] $types
     */
    public function setKeyTypes(array $types) : void
    {
        $this->keyTypes = $types;
    }

    /**
     * Registers the type, or set of types, to which a *key* in this type of collection can belong.
     *
     * @return TypeInterface[]
     */
    public function getKeyTypes() : array
    {
        return $this->keyTypes;
    }

    /**
     * Returns a human-readable representation for this type.
     */
    public function __toString() : string
    {
        $name = (string) $this->getName();

        $keyTypes = [];
        foreach ($this->getKeyTypes() as $type) {
            $keyTypes[] = (string) $type;
        }

        $types = [];
        foreach ($this->getTypes() as $type) {
            $types[] = (string) $type;
        }

        if (count($types) > 0) {
            $name .= '<' . ($keyTypes ? implode('|', $keyTypes) . ',' : '') . implode('|', $types) . '>';
        }

        return $name;
    }
}
