<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use phpDocumentor\Guides\RestructuredText\Nodes\MainNode;
use phpDocumentor\Guides\RestructuredText\Renderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\RestructuredText\Renderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRenderer;
use phpDocumentor\Guides\RestructuredText\Templates\TemplateRenderer;
use function count;

class DocumentNodeRenderer implements NodeRenderer, FullDocumentNodeRenderer
{
    /** @var DocumentNode */
    private $document;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(DocumentNode $document, TemplateRenderer $templateRenderer)
    {
        $this->document         = $document;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return (new BaseDocumentRender($this->document))->render();
    }

    public function renderDocument() : string
    {
        return $this->templateRenderer->render('document.tex.twig', [
            'isMain' => $this->isMain(),
            'document' => $this->document,
            'body' => $this->render(),
        ]);
    }

    private function isMain() : bool
    {
        return count($this->document->getNodes(static function ($node) {
            return $node instanceof MainNode;
        })) !== 0;
    }
}
