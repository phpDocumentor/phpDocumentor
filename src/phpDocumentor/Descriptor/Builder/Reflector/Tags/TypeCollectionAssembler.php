<?php


namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Collection as DescriptorCollection;
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
        foreach ($data as &$type) {
            switch($type) {
                case 'string':
                    $type = new StringDescriptor();
                    break;
                default:
                    $type = new UnknownTypeDescriptor($type);
            }
        }

        return new DescriptorCollection(array('dunno' => new UnknownTypeDescriptor('HELL IF I KNOW?!')));
    }
}
