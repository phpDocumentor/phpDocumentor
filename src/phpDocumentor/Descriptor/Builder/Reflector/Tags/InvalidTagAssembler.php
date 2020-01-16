<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;

final class InvalidTagAssembler extends AssemblerAbstract
{
    /**
     * @see $data
     *
     * @param InvalidTag $data
     */
    public function create($data) : TagDescriptor
    {
        $descriptor = new TagDescriptor($data->getName());
        $descriptor->setDescription((string) $data);
        $descriptor->getErrors()->add(
            $data->getException()->getMessage()
        );

        return $descriptor;
    }
}
