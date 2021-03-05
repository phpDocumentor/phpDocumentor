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

namespace phpDocumentor\Guides\Renderers\LaTeX;

use InvalidArgumentException;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\TocNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class TocNodeRenderer implements NodeRenderer
{
    /** @var Environment */
    private $environment;

    /** @var Renderer */
    private $renderer;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        $this->renderer = $environment->getRenderer();
    }

    public function render(Node $node) : string
    {
        if ($node instanceof TocNode === false) {
            throw new InvalidArgumentException('Invalid node presented');
        }

        $tocItems = [];

        foreach ($node->getFiles() as $file) {
            $reference = $this->environment->resolve('doc', $file);

            if ($reference === null) {
                continue;
            }

            $url = $this->environment->relativeUrl($reference->getUrl());

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
