<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\References;

use phpDocumentor\Guides\References\Resolver\Resolver;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Span\CrossReferenceNode;

/**
 * @link https://docs.readthedocs.io/en/stable/guides/cross-referencing-with-sphinx.html
 * @link https://www.sphinx-doc.org/en/master/usage/restructuredtext/domains.html
 */
final class ReferenceResolver
{
    /** @var iterable<Resolver> */
    private $resolvers;

    /** @param iterable<Resolver> $resolvers */
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
