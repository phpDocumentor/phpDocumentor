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

namespace phpDocumentor\Guides\NodeRenderers\LaTeX;

use InvalidArgumentException;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TocNode;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\UrlGenerator;

use function ltrim;

class TocNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;
    private UrlGenerator $urlGenerator;

    public function __construct(Renderer $renderer, UrlGenerator $urlGenerator)
    {
        $this->renderer = $renderer;
        $this->urlGenerator = $urlGenerator;
    }

    public function render(Node $node, RenderContext $environment): string
    {
        if ($node instanceof TocNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        $tocItems = [];

        foreach ($node->getFiles() as $file) {
            $metaEntry = $environment->getMetas()->get(ltrim($file, '/'));
            if ($metaEntry === null) {
                continue;
            }

            $tocItems[] = ['url' => $this->urlGenerator->relativeUrl($metaEntry->getUrl())];
        }

        return $this->renderer->render(
            'toc.tex.twig',
            [
                'tocNode' => $node,
                'tocItems' => $tocItems,
            ]
        );
    }

    public function supports(Node $node): bool
    {
        return $node instanceof TocNode;
    }
}
