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
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TocNode;
use phpDocumentor\Guides\ReferenceBuilder;
use phpDocumentor\Guides\Renderer;

class TocNodeRenderer implements NodeRenderer
{
    /** @var Renderer */
    private $renderer;

    /** @var ReferenceBuilder */
    private $referenceRegistry;

    public function __construct(Renderer $renderer, ReferenceBuilder $referenceRegistry)
    {
        $this->renderer = $renderer;
        $this->referenceRegistry = $referenceRegistry;
    }

    public function render(Node $node, Environment $environment): string
    {
        if ($node instanceof TocNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        $tocItems = [];

        foreach ($node->getFiles() as $file) {
            $reference = $this->referenceRegistry->resolve(
                $environment,
                'doc',
                $file,
                $environment->getMetaEntry()
            );

            if ($reference === null) {
                continue;
            }

            $url = $environment->relativeUrl($reference->getUrl());

            $tocItems[] = ['url' => $url];
        }

        return $this->renderer->render(
            'toc.tex.twig',
            [
                'tocNode' => $node,
                'tocItems' => $tocItems,
            ]
        );
    }
}
