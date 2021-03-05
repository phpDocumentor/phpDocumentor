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

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\Renderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class DocumentNodeRenderer implements NodeRenderer, FullDocumentNodeRenderer
{
    /** @var Renderer */
    private $renderer;

    /** @var Environment */
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
        $this->renderer = $this->environment->getRenderer();
    }

    public function render(Node $node) : string
    {
        if ($node instanceof DocumentNode === false) {
            throw new \InvalidArgumentException('Invalid node presented');
        }

        return (new BaseDocumentRender())->render($node);
    }

    public function renderDocument(DocumentNode $node) : string
    {
        $this->renderer->setGuidesEnvironment($this->environment);
        $this->renderer->setDestination($this->environment->getUrl());

        $output = $this->render($node);

        return $this->renderer->render(
            'document.html.twig',
            [
                'headerNodes' => $this->assembleHeader($node),
                'bodyNodes' => $output,
            ]
        );
    }

    private function assembleHeader(DocumentNode $node) : string
    {
        $headerNodes = '';

        foreach ($node->getHeaderNodes() as $headerNode) {
            $headerNodes .= $headerNode->render() . "\n";
        }

        return $headerNodes;
    }
}
