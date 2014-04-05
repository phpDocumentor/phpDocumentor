<?php

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Collection as DescriptorCollection;
use phpDocumentor\Descriptor\Type\BooleanDescriptor;
use phpDocumentor\Descriptor\Type\CollectionDescriptor;
use phpDocumentor\Descriptor\Type\FloatDescriptor;
use phpDocumentor\Descriptor\Type\IntegerDescriptor;
use phpDocumentor\Descriptor\Type\StringDescriptor;
use phpDocumentor\Descriptor\Type\UnknownTypeDescriptor;
use phpDocumentor\Reflection\DocBlock\Type\Collection;

class TypeCollectionAssembler extends AssemblerAbstract
{
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
            $isArray = (substr($type, -2) == '[]');
            if ($isArray) {
                $type = substr($type, 0, -2);
            }

            switch($type) {
                case 'string':
                    $descriptor = new StringDescriptor();
                    break;
                case 'integer':
                case 'int':
                    $descriptor = new IntegerDescriptor();
                    break;
                case 'float':
                    $descriptor = new FloatDescriptor();
                    break;
                case 'boolean':
                case 'bool':
                    $descriptor = new BooleanDescriptor();
                    break;
                default:
                    $descriptor = new UnknownTypeDescriptor($type);
            }

            if ($isArray) {
                $arrayDescriptor = new CollectionDescriptor('array');
                $arrayDescriptor->setTypes(array($descriptor));
                $descriptor = $arrayDescriptor;
            }

            $collection->add($descriptor);
        }

        return $collection;
    }
}
