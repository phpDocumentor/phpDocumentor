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

use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\MainNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\Renderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use function count;

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
        return $this->renderer->render(
            'document.tex.twig',
            [
                'isMain' => $this->isMain(),
                'document' => $this->document,
                'body' => $this->render(),
            ]
        );
    }

    private function isMain() : bool
    {
        $nodes = $this->document->getNodes(
            static function ($node) {
                return $node instanceof MainNode;
            }
        );

        return count($nodes) !== 0;
    }
}
