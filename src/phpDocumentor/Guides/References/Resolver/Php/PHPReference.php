<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\References\Resolver\Php;

use phpDocumentor\Guides\References\ResolvedReference;
use phpDocumentor\Guides\References\Resolver\Resolver;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Span\CrossReferenceNode;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Transformer\Router\Router;

use function ltrim;

/**
 * php domain reference resolver
 *
 * Resolves the references in `php` domain. All elements supported by phpDocument's api component are available in the
 * router, and can be referenced from guides.
 *
 * To reference a class you can use the example below.
 *
 * ```
 *   :php:class:`\phpDocumentor\Guides\References\Resolver\Php\PHPReference`
 * ```
 */
final class PHPReference implements Resolver
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function supports(CrossReferenceNode $node, RenderContext $context): bool
    {
        return $node->getDomain() === 'php';
    }

    public function resolve(CrossReferenceNode $node, RenderContext $context): ?ResolvedReference
    {
        $fqsen = ltrim($node->getUrl(), '\\');
        $url = $this->router->generate(new Fqsen('\\' . $fqsen));

        return new ResolvedReference(
            $context->getCurrentFileName(),
            $node->getUrl(),
            $url,
            [],
            ['title' => $node->getUrl()]
        );
    }
}
