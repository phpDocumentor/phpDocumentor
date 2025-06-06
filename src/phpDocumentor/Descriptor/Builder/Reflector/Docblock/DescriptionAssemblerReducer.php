<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Docblock;

use phpDocumentor\Descriptor\Builder\AssemblerAbstract;
use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\PropertyHookDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag;
use Webmozart\Assert\Assert;

/** @extends AssemblerAbstract<Descriptor, object> */
final class DescriptionAssemblerReducer extends AssemblerAbstract implements AssemblerReducer
{
    /** @return ElementInterface|TagDescriptor|null */
    public function create(object $data, Descriptor|null $descriptor = null): Descriptor|null
    {
        if ($descriptor === null) {
            return null;
        }

        Assert::isInstanceOfAny(
            $descriptor,
            [ElementInterface::class, TagDescriptor::class, PropertyHookDescriptor::class],
        );

        /** @phpstan-var ElementInterface|TagDescriptor $descriptor */
        $description = new DescriptionDescriptor(
            $data->getDescription(),
            $data->getDescription() !== null ? $this->createTags($data->getDescription()->getTags()) : [],
        );

        $descriptor->setDescription($description);

        return $descriptor;
    }

    /**
     * @param Tag[] $tags
     *
     * @return list<TagDescriptor|null>
     */
    private function createTags(array $tags): array
    {
        $result = [];
        foreach ($tags as $tag) {
            $result[] = $this->builder->buildDescriptor($tag, TagDescriptor::class);
        }

        return $result;
    }
}
