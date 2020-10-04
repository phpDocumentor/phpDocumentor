<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\AssemblerAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Descriptor\Validation\Error;
use phpDocumentor\Reflection\DocBlock\Description;
use function sprintf;

/**
 * @extends AssemblerAbstract<TagDescriptor, \phpDocumentor\Reflection\DocBlock\Tags\InvalidTag>
 */
final class InvalidTagAssembler extends AssemblerAbstract
{
    public function create(object $data) : TagDescriptor
    {
        $descriptor = new TagDescriptor($data->getName());
        $descriptor->setDescription(new DescriptionDescriptor(new Description((string) $data), []));
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
