<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers\Html;

use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Renderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\Renderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use phpDocumentor\Guides\TemplateRenderer;
use phpDocumentor\Transformer\Writer\Twig\Extension;

class DocumentNodeRenderer implements NodeRenderer, FullDocumentNodeRenderer
{
    /** @var DocumentNode */
    private $document;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(DocumentNode $document, TemplateRenderer $templateRenderer)
    {
        $this->document = $document;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        return (new BaseDocumentRender($this->document))->render();
    }

    public function renderDocument() : string
    {
        $this->templateRenderer->setDestination($this->document->getEnvironment()->getCurrentFileName());

        $output = $this->render();

        return $this->templateRenderer->render('document.html.twig', [
            'headerNodes' => $this->assembleHeader(),
            'bodyNodes' => $output,
        ]);
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
