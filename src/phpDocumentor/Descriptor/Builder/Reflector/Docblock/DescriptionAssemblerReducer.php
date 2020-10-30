<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Docblock;

use phpDocumentor\Descriptor\Builder\AssemblerAbstract;
use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag;

/**
 * @extends AssemblerAbstract<DescriptorAbstract, object>
 */
final class DescriptionAssemblerReducer extends AssemblerAbstract implements AssemblerReducer
{
    /**
     * @param DescriptorAbstract|null $descriptor
     *
     * @return DescriptorAbstract|null
     */
    public function create(object $data, ?Descriptor $descriptor = null) : ?Descriptor
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
     *
     * @return list<TagDescriptor|null>
     */
    private function createTags(array $tags) : array
    {
        $result = [];
        foreach ($tags as $tag) {
            $result[] = $this->builder->buildDescriptor($tag, TagDescriptor::class);
        }

        return $result;
    }
}
