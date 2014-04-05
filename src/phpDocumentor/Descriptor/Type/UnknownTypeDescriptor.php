<?php

namespace phpDocumentor\Descriptor\Type;

use phpDocumentor\Descriptor\Interfaces\TypeInterface;

/**
 * This class represents any Type that could not be identified.
 *
 * Sometimes DocBlocks refer to types, such as classes, that are outside the generated project's scope; in these
 * cases we want to identify them as such with an unknown type descriptor.
 */
class UnknownTypeDescriptor implements TypeInterface
{
    /** @var string Name/Identifier of the unknown type */
    protected $name;

    /**
     * Creates an unknown type with the given name.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name for this identifier.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->getName();
    }
}