<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\Environment;
use phpDocumentor\Guides\RestructuredText\ErrorManager;
use phpDocumentor\Guides\RestructuredText\Renderers\FullDocumentNodeRenderer;
use Exception;
use function array_unshift;
use function count;
use function is_string;
use function sprintf;

class DocumentNode extends Node
{
    /** @var Environment */
    protected $environment;

    /** @var Configuration */
    protected $configuration;

    /** @var ErrorManager */
    protected $errorManager;

    /** @var Node[] */
    protected $headerNodes = [];

    /** @var Node[] */
    protected $nodes = [];

    public function __construct(Environment $environment)
    {
        parent::__construct();

        $this->environment   = $environment;
        $this->configuration = $environment->getConfiguration();
        $this->errorManager  = $environment->getErrorManager();
    }

    public function getEnvironment() : Environment
    {
        return $this->environment;
    }

    public function getConfiguration() : Configuration
    {
        return $this->configuration;
    }

    /**
     * @return Node[]
     */
    public function getHeaderNodes() : array
    {
        return $this->headerNodes;
    }

    public function renderDocument() : string
    {
        $renderedDocument = $this->doRenderDocument();

        $this->postRenderValidate();

        return $renderedDocument;
    }

    /**
     * @return Node[]
     */
    public function getNodes(?callable $function = null) : array
    {
        $nodes = [];

        if ($function === null) {
            return $this->nodes;
        }

        foreach ($this->nodes as $node) {
            if (! $function($node)) {
                continue;
            }

            $nodes[] = $node;
        }

        return $nodes;
    }

    public function getTitle() : ?string
    {
        foreach ($this->nodes as $node) {
            if ($node instanceof TitleNode && $node->getLevel() === 1) {
                return $node->getValue()->render() . '';
            }
        }

        return null;
    }

    /**
     * @return mixed[]
     */
    public function getTocs() : array
    {
        $tocs = [];

        $nodes = $this->getNodes(static function ($node) {
            return $node instanceof TocNode;
        });

        /** @var TocNode $toc */
        foreach ($nodes as $toc) {
            $files = $toc->getFiles();

            foreach ($files as &$file) {
                $file = $this->environment->canonicalUrl($file);
            }

            $tocs[] = $files;
        }

        return $tocs;
    }

    /**
     * @return string[][]
     */
    public function getTitles() : array
    {
        $titles = [];
        $levels = [&$titles];

        foreach ($this->nodes as $node) {
            if (! ($node instanceof TitleNode)) {
                continue;
            }

            $level       = $node->getLevel();
            $text        = $node->getValue()->getValue();
            $redirection = $node->getTarget();
            $value       = $redirection !== '' ? [$text, $redirection] : $text;

            if (! isset($levels[$level - 1])) {
                continue;
            }

            $parent         = &$levels[$level - 1];
            $element        = [$value, []];
            $parent[]       = $element;
            $levels[$level] = &$parent[count($parent) - 1][1];
        }

        return $titles;
    }

    /**
     * @param string|Node $node
     */
    public function addNode($node) : void
    {
        if (is_string($node)) {
            $node = new RawNode($node);
        }

        $this->nodes[] = $node;
    }

    public function prependNode(Node $node) : void
    {
        array_unshift($this->nodes, $node);
    }

    public function addHeaderNode(Node $node) : void
    {
        $this->headerNodes[] = $node;
    }

    public function addCss(string $css) : void
    {
        $css = $this->environment->relativeUrl($css);

        if ($css === null) {
            throw new Exception(sprintf('Could not get relative url for css %s', $css));
        }

        $this->addHeaderNode($this->environment->getNodeFactory()->createRawNode(
            $this->environment->getTemplateRenderer()->render('stylesheet-link.html.twig', ['css' => $css])
        ));
    }

    public function addJs(string $js) : void
    {
        $js = $this->environment->relativeUrl($js);

        if ($js === null) {
            throw new Exception(sprintf('Could not get relative url for js %s', $js));
        }

        $this->addHeaderNode($this->environment->getNodeFactory()->createRawNode(
            $this->environment->getTemplateRenderer()->render('javascript.html.twig', ['js' => $js])
        ));
    }

    public function addFavicon(string $url = '/favicon.ico') : void
    {
        $url = $this->environment->relativeUrl($url);

        if ($url === null) {
            throw new Exception(sprintf('Could not get relative url for favicon %s', $url));
        }

        $this->addHeaderNode($this->environment->getNodeFactory()->createRawNode(
            $this->environment->getTemplateRenderer()->render('favicon.html.twig', ['url' => $url])
        ));
    }

    protected function doRenderDocument() : string
    {
        /** @var FullDocumentNodeRenderer $renderer */
        $renderer = $this->getRenderer();

        return $renderer->renderDocument();
    }

    private function postRenderValidate() : void
    {
        if ($this->configuration->getIgnoreInvalidReferences() !== false) {
            return;
        }

        $currentFileName = $this->environment->getCurrentFileName();

        foreach ($this->environment->getInvalidLinks() as $invalidLink) {
            $this->errorManager->error(sprintf(
                'Found invalid reference "%s"%s',
                $invalidLink->getName(),
                $currentFileName !== '' ? sprintf(' in file "%s"', $currentFileName) : ''
            ));
        }
    }
}
