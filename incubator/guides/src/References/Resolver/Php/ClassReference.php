<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\References\Resolver\Php;

use phpDocumentor\Guides\References\ResolvedReference;
use phpDocumentor\Guides\References\Resolver\Resolver;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Span\CrossReferenceNode;

class ClassReference implements Resolver
{

    public function supports(CrossReferenceNode $node, RenderContext $context): bool
    {
        return $node->getDomain() === 'php' && $node->getRole() === 'class';
    }

    public function resolve(CrossReferenceNode $node, RenderContext $context): ?ResolvedReference
    {
        // TODO: The location of the resolved class should come from the TOC and not like this
        $classPath = sprintf('%s/classes/%s.html', '', str_replace('\\', '-', $node->getUrl()));

        return new ResolvedReference(
            $context->getCurrentFileName(),
            $node->getUrl(),
            $classPath,
            [],
            ['title' => $node->getUrl()]
        );
    }
}
