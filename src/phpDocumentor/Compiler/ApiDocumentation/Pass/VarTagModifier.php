<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Compiler\ApiDocumentation\ApiDocumentationPass;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\EnumDescriptor;
use phpDocumentor\Descriptor\Interfaces\ConstantInterface;
use phpDocumentor\Descriptor\Interfaces\PropertyInterface;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Pipeline\Attribute\Stage;

use function array_values;

#[Stage(
    'phpdoc.pipeline.api_documentation.compile',
    9000,
    'Filter named var tags for property and constant groups',
)]
final class VarTagModifier extends ApiDocumentationPass
{
    protected function process(ApiSetDescriptor $subject): ApiSetDescriptor
    {
        foreach ($subject->getIndex('classes')->filter(ClassDescriptor::class) as $class) {
            foreach ($class->getConstants() as $constant) {
                $this->filterVarTags($constant);
            }

            foreach ($class->getProperties() as $property) {
                $this->filterVarTags($property);
            }
        }

        foreach ($subject->getIndex('traits')->filter(TraitDescriptor::class) as $class) {
            foreach ($class->getConstants() as $constant) {
                $this->filterVarTags($constant);
            }

            foreach ($class->getProperties() as $property) {
                $this->filterVarTags($property);
            }
        }

        foreach ($subject->getIndex('enums')->filter(EnumDescriptor::class) as $class) {
            foreach ($class->getConstants() as $constant) {
                $this->filterVarTags($constant);
            }
        }

        return $subject;
    }

    private function filterVarTags(ConstantInterface|PropertyInterface $descriptor): void
    {
        $tags = $descriptor->getTags()->fetch('var', Collection::fromClassString(TagDescriptor::class));

        foreach ($tags->filter(VarDescriptor::class) as $index => $tag) {
            if ($tag->getVariableName() === '') {
                continue;
            }

            if ($tag->getVariableName() === $descriptor->getName()) {
                continue;
            }

            unset($tags[$index]);
        }

        $descriptor->getTags()['var'] = new Collection(array_values($tags->getAll()));
    }
}
