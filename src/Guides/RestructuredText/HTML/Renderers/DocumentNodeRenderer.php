<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Renderers;

use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use phpDocumentor\Guides\RestructuredText\Renderers\DocumentNodeRenderer as BaseDocumentRender;
use phpDocumentor\Guides\RestructuredText\Renderers\FullDocumentNodeRenderer;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRenderer;
use phpDocumentor\Guides\RestructuredText\TemplateRenderer;
use phpDocumentor\Transformer\Writer\Twig\Extension;

class DocumentNodeRenderer implements NodeRenderer, FullDocumentNodeRenderer
{
    /** @var DocumentNode */
    private $document;

    /** @var TemplateRenderer */
    private $templateRenderer;

    /** @var string */
    private $subFolderInProject;

    public function __construct(
        DocumentNode $document,
        TemplateRenderer $templateRenderer,
        string $subFolderInProject
    ) {
        $this->document = $document;
        $this->templateRenderer = $templateRenderer;
        $this->subFolderInProject = trim($subFolderInProject, '/');
    }

    public function render() : string
    {
        return (new BaseDocumentRender($this->document))->render();
    }

    public function renderDocument() : string
    {
        $this->setCurrentFileNameInTwigToDetermineRelativePathsToDocumentationRoot($this->subFolderInProject);

        $output = $this->render();

        return $this->templateRenderer->render('document.html.twig', [
            'headerNodes' => $this->assembleHeader(),
            'bodyNodes' => $output,
            'menu' => $this->assembleMenu($this->subFolderInProject),
        ]);
    }

    private function assembleMenu(string $urlPrefix) : array
    {
        $metas = $this->document->getEnvironment()->getMetas();

        $index = $metas->get('index');
        $menu = [
            'label' => $index->getTitle(),
            'path' => $urlPrefix . '/' . $index->getUrl(),
            'items' => []
        ];
        foreach ($index->getTocs()[0] as $url) {
            $meta = $metas->get($url);
            $menu['items'][] = [
                'label' => $meta->getTitle(),
                'path' => $urlPrefix . '/' . $meta->getUrl(),
                'items' => []
            ];
        }

        return $menu;
    }

    private function assembleHeader() : string
    {
        $headerNodes = '';

        foreach ($this->document->getHeaderNodes() as $node) {
            $headerNodes .= $node->render() . "\n";
        }
        return $headerNodes;
    }

    private function setCurrentFileNameInTwigToDetermineRelativePathsToDocumentationRoot(string $urlPrefix) : void
    {
        /** @var Extension $extension */
        $extension = $this->templateRenderer->getTemplateEngine()->getExtension(Extension::class);
        $extension->setDestination($urlPrefix . '/' . $this->document->getEnvironment()->getCurrentFileName());
    }
}
