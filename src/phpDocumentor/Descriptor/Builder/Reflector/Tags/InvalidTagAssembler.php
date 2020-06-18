<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Descriptor\Validation\Error;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use function sprintf;

final class InvalidTagAssembler extends AssemblerAbstract
{
    /**
     * @see $data
     *
     * @param InvalidTag $data
     */
    public function create(object $data) : TagDescriptor
    {
        $descriptor = new TagDescriptor($data->getName());
        $descriptor->setDescription(new Description((string) $data));
        $descriptor->getErrors()->add(
            new Error(
                'ERROR',
                sprintf(
                    'Tag "%s" with body "%s" has error %s',
                    $data->getName(),
                    $data->render(),
                    $data->getException() === null ? '' : $data->getException()->getMessage()
                ),
                null
            )
        );

        return $descriptor;
    }
}
