<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\References;

use phpDocumentor\Guides\References\Resolver\Resolver;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Span\CrossReferenceNode;

final class ReferenceResolver
{
    /**
     * @var iterable<Resolver>
     */
    private $resolvers;

    public function __construct(iterable $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    public function resolve(CrossReferenceNode $node, RenderContext $context): ?ResolvedReference
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($node, $context)) {
                return $resolver->resolve($node, $context);
            }
        }

        return null;
    }
}
