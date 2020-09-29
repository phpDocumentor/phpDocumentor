<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Docblock;

use phpDocumentor\Descriptor\Builder\AssemblerAbstract;
use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\DocBlock\InlineTagDescriptor;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;

final class DescriptionAssemblerReducer extends AssemblerAbstract implements AssemblerReducer
{
    /**
     * @template T of Descriptor
     *
     * @param BaseTag|DocBlock $data
     * @param T $descriptor
     * @return T
     */
    public function create(object $data, Descriptor $descriptor = null) : ?Descriptor
    {
        if ($descriptor === null) {
            return null;
        }

        $description = new DescriptionDescriptor(
            $data->getDescription(),
            $data->getDescription() !== null ? $this->createTags($data->getDescription()->getTags()) : []
        );

        $descriptor->setDescription($description);

        return $descriptor;
    }

    /**
     * @param Tag[] $tags
     * @return InlineTagDescriptor[]
     */
    private function createTags(array $tags) : array
    {

        $result = [];
        foreach ($tags as $tag) {
            $result[] = $this->builder->buildDescriptor($tag);
        }

        return array_filter($result);
    }
}
