<?php

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Collection as DescriptorCollection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Type\CollectionDescriptor;
use phpDocumentor\Descriptor\Type\UnknownTypeDescriptor;
use phpDocumentor\Reflection\DocBlock\Type\Collection;

/**
 * Creates a Collection of type-related value objects for the given Type Collection from the Reflector.
 */
class TypeCollectionAssembler extends AssemblerAbstract
{
    /** @var string[] a mapping of types to class names of the Value Object class that describes each type */
    protected $typeToValueObjectClassName = array(
        'string'  => 'phpDocumentor\Descriptor\Type\StringDescriptor',
        'int'     => 'phpDocumentor\Descriptor\Type\IntegerDescriptor',
        'integer' => 'phpDocumentor\Descriptor\Type\IntegerDescriptor',
        'float'   => 'phpDocumentor\Descriptor\Type\FloatDescriptor',
        'boolean' => 'phpDocumentor\Descriptor\Type\BooleanDescriptor',
        'bool'    => 'phpDocumentor\Descriptor\Type\BooleanDescriptor',
    );

    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Collection $data
     *
     * @return DescriptorCollection
     */
    public function create($data)
    {
        $collection = new DescriptorCollection();

        foreach ($data as $type) {
            $collection->add($this->createDescriptorForType($type));
        }

        return $collection;
    }

    /**
     * Creates a Type ValueObject (Descriptor) for the provided type string.
     *
     * @param string $type
     *
     * @return DescriptorAbstract
     */
    protected function createDescriptorForType($type)
    {
        if (!$this->isArrayNotation($type)) {
            $className = $this->findClassNameForType($type);

            return $className ? new $className() : new UnknownTypeDescriptor($type);
        }

        $type       = $this->extractTypeFromArrayNotation($type);
        $className  = $this->findClassNameForType($type);
        $descriptor = $className ? new $className() : new UnknownTypeDescriptor($type);
        $descriptor = $this->convertToArrayDescriptor($descriptor);

        return $descriptor;
    }

    /**
     * Detects if the given string representing a type equals an array.
     *
     * @param string $type
     *
     * @return boolean
     */
    protected function isArrayNotation($type)
    {
        return (substr($type, -2) == '[]');
    }

    /**
     * Returns the value-type from an array notation.
     *
     * @param string $type
     *
     * @return string
     */
    protected function extractTypeFromArrayNotation($type)
    {
        return substr($type, 0, -2);
    }

    /**
     * Wraps the given Descriptor inside a Collection Descriptor of type array and returns that.
     *
     * @param DescriptorAbstract $descriptor
     *
     * @return CollectionDescriptor
     */
    protected function convertToArrayDescriptor($descriptor)
    {
        $arrayDescriptor = new CollectionDescriptor('array');
        $arrayDescriptor->setTypes(array($descriptor));
        $arrayDescriptor->setKeyTypes(array('mixed'));

        return $arrayDescriptor;
    }

    /**
     * Returns the class name of the Value Object class associated with a given type or false if the type is unknown.
     *
     * @param string $type
     *
     * @return string|boolean
     */
    protected function findClassNameForType($type)
    {
        $className = (isset($this->typeToValueObjectClassName[$type]))
            ? $this->typeToValueObjectClassName[$type]
            : false;

        return $className;
    }
}
