<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Traits;

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Interfaces\AttributedInterface;
use phpDocumentor\Descriptor\Tag\DeprecatedDescriptor;
use phpDocumentor\Descriptor\ValueObjects\Deprecation;
use phpDocumentor\Reflection\DocBlock\Description;

use function array_map;
use function trim;

trait CanBeDeprecated
{
    /**
     * Checks whether this element is deprecated.
     */
    public function isDeprecated(): bool
    {
        return $this->isDeprecatedByTag() || $this->isDeprecatedByAttribute();
    }

    private function isDeprecatedByTag(): bool
    {
        return isset($this->tags['deprecated']);
    }

    /**
     * @phpstan-assert-if-true AttributedInterface $this
     * @psalm-assert-if-true AttributedInterface $this`[
     */
    private function isDeprecatedByAttribute(): bool
    {
        if ($this instanceof AttributedInterface) {
            foreach ($this->getAttributes() as $attribute) {
                if ((string) $attribute->getFullyQualifiedStructuralElementName() === '\Deprecated') {
                    return true;
                }
            }
        }

        return false;
    }

    /** @return Deprecation[] */
    public function getDeprecations(): array
    {
        $deprecations = [];
        if ($this->isDeprecatedByTag()) {
            /** @var DeprecatedDescriptor[] $tags */
            $tags = $this->getTags()->get('deprecated')->getAll();
            $deprecations = array_map(
                static function (DeprecatedDescriptor $tag): Deprecation {
                    return new Deprecation($tag->getDescription(), $tag->getVersion());
                },
                $tags,
            );
        }

        if ($this->isDeprecatedByAttribute()) {
            foreach ($this->getAttributes() as $attribute) {
                if ((string) $attribute->getFullyQualifiedStructuralElementName() !== '\Deprecated') {
                    continue;
                }

                $deprecations[] = new Deprecation(
                    new DescriptionDescriptor(
                        $this->createDescription(
                            ($attribute->getArguments()['message'] ?? $attribute->getArguments()[0])?->getValue(),
                        ),
                        [],
                    ),
                    $attribute->getArguments()['since'] ?? $attribute->getArguments()[1]?->getValue(),
                );
            }
        }

        return $deprecations;
    }

    private function createDescription(string|null $param): Description
    {
        if ($param === null) {
            return new Description('', []);
        }

        return new Description(
            trim($param, '\''),
            [],
        );
    }
}
