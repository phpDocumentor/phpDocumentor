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

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\TocNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class TocNodeRenderer implements NodeRenderer
{
    /** @var Environment */
    private $environment;

    /** @var TocNode */
    private $tocNode;

    /** @var Renderer */
    private $renderer;

    public function __construct(TocNode $tocNode)
    {
        $this->environment = $tocNode->getEnvironment();
        $this->tocNode = $tocNode;
        $this->renderer = $tocNode->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        $tocItems = [];

        foreach ($this->tocNode->getFiles() as $file) {
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
                'tocNode' => $this->tocNode,
                'tocItems' => $tocItems,
            ]
        );
    }
}
