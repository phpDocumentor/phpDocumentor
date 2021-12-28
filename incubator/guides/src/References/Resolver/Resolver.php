<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\References\Resolver;

use phpDocumentor\Guides\References\ResolvedReference;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Span\CrossReferenceNode;

interface Resolver
{
    public function supports(CrossReferenceNode $node, RenderContext $context): bool;

    public function resolve(CrossReferenceNode $node, RenderContext $context): ?ResolvedReference;
}
