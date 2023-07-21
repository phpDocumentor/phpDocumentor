<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Twig;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Path;
use phpDocumentor\Reflection\DocBlock\Tags\Reference;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;

/**
 * Renders an HTML anchor pointing to the location of the provided element.
 */
interface LinkRendererInterface
{
    /** @param array<Type>|Type|DescriptorAbstract|Fqsen|Reference\Reference|Path|string|iterable<mixed> $value */
    public function supports($value): bool;

    /**
     * @param array<Type>|Type|DescriptorAbstract|Fqsen|Reference\Reference|Path|string|iterable<mixed> $value
     *
     * @return string|list<string>
     */
    public function render($value, string $presentation);
}
