<?php

declare(strict_types=1);

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides;

use Doctrine\RST\Formats\Format;
use Doctrine\RST\Nodes\CodeNode;
use Doctrine\RST\Nodes\SpanNode;
use Doctrine\RST\Renderers\CallableNodeRendererFactory;
use Doctrine\RST\Renderers\NodeRendererFactory;
use Doctrine\RST\Templates\TemplateRenderer;
use phpDocumentor\Guides\CI\UrlChecker;

/**
 * Class SymfonyHTMLFormat.
 */
final class SymfonyHTMLFormat implements Format
{
    protected $templateRenderer;
    private $htmlFormat;
    /** @var UrlChecker|null */
    private $urlChecker;

    public function __construct(TemplateRenderer $templateRenderer, Format $HTMLFormat, ?UrlChecker $urlChecker = null)
    {
        $this->templateRenderer = $templateRenderer;
        $this->htmlFormat = $HTMLFormat;
        $this->urlChecker = $urlChecker;
    }

    public function getFileExtension(): string
    {
        return Format::HTML;
    }

    public function getDirectives(): array
    {
        return $this->htmlFormat->getDirectives();
    }

    /**
     * @return NodeRendererFactory[]
     */
    public function getNodeRendererFactories(): array
    {
        $nodeRendererFactories = $this->htmlFormat->getNodeRendererFactories();

        $nodeRendererFactories[CodeNode::class] = new CallableNodeRendererFactory(
            function (CodeNode $node) {
                return new Renderers\CodeNodeRenderer(
                    $node,
                    $this->templateRenderer
                );
            }
        );

        $nodeRendererFactories[SpanNode::class] = new CallableNodeRendererFactory(
            function (SpanNode $node) {
                return new Renderers\SpanNodeRenderer(
                    $node->getEnvironment(),
                    $node,
                    $this->templateRenderer,
                    $this->urlChecker
                );
            }
        );

        return $nodeRendererFactories;
    }
}
