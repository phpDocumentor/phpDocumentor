<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers;

use Doctrine\RST\Nodes\DocumentNode;
use Doctrine\RST\Templates\TemplateRenderer;
use phpDocumentor\Transformer\Writer\Twig\Extension;

final class DocumentNodeRenderer extends \Doctrine\RST\HTML\Renderers\DocumentNodeRenderer
{
    /**
     * @var DocumentNode
     */
    private $document;

    /**
     * @var TemplateRenderer
     */
    private $templateRenderer;

    /**
     * @var string
     */
    private $subFolderInProject;

    public function __construct(
        DocumentNode $document,
        TemplateRenderer $templateRenderer,
        string $subFolderInProject
    ) {
        parent::__construct($document, $templateRenderer);

        $this->document = $document;
        $this->templateRenderer = $templateRenderer;
        $this->subFolderInProject = trim($subFolderInProject, '/');
    }

    public function renderDocument() : string
    {
        $this->setCurrentFileNameInTwigToDetermineRelativePathsToDocumentationRoot($this->subFolderInProject);

        return $this->templateRenderer->render('document.html.twig', [
            'headerNodes' => $this->assembleHeader(),
            'bodyNodes' => $this->render(),
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
        $extension = $this->document->getConfiguration()->getTemplateEngine()->getExtension(Extension::class);
        $extension->setDestination($urlPrefix . '/' . $this->document->getEnvironment()->getCurrentFileName());
    }
}
