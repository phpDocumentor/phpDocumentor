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

use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\Renderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;

class DocumentNodeRenderer implements NodeRenderer, FullDocumentNodeRenderer
{
    /** @var DocumentNode */
    private $document;

    /** @var Renderer */
    private $renderer;

    public function __construct(DocumentNode $document)
    {
        $this->document = $document;
        $this->renderer = $document->getEnvironment()->getRenderer();
    }

    public function render() : string
    {
        return (new BaseDocumentRender($this->document))->render();
    }

    public function renderDocument() : string
    {
        $this->renderer->setDestination($this->document->getEnvironment()->getUrl());

        $output = $this->render();

        return $this->renderer->render(
            'document.html.twig',
            [
                'headerNodes' => $this->assembleHeader(),
                'bodyNodes' => $output,
            ]
        );
    }

    private function assembleHeader() : string
    {
        $headerNodes = '';

        foreach ($this->document->getHeaderNodes() as $node) {
            $headerNodes .= $node->render() . "\n";
        }

        return $headerNodes;
    }
}
