<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\RestructuredText\HTML;

use phpDocumentor\Guides\RestructuredText;
use phpDocumentor\Guides\RestructuredText\Formats\Format;
use phpDocumentor\Guides\RestructuredText\HTML\Renderers\DocumentNodeRenderer;
use phpDocumentor\Guides\RestructuredText\Nodes\CodeNode;
use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use phpDocumentor\Guides\RestructuredText\Nodes\SpanNode;
use phpDocumentor\Guides\RestructuredText\Renderers\CallableNodeRendererFactory;
use phpDocumentor\Guides\RestructuredText\Renderers\NodeRendererFactory;
use phpDocumentor\Guides\RestructuredText\TemplateRenderer;

final class HTMLFormat implements Format
{
    private $templateRenderer;
    private $globalTemplatesPath;
    private $subFolder;

    public function __construct(
        TemplateRenderer $templateRenderer,
        string $globalTemplatesPath,
        string $subFolder
    ) {
        $this->templateRenderer = $templateRenderer;
        $this->globalTemplatesPath = $globalTemplatesPath;
        $this->subFolder = $subFolder;
    }

    public function getFileExtension() : string
    {
        return Format::HTML;
    }

    public function getDirectives() : array
    {
        return [
            new RestructuredText\HTML\Directives\Image(),
            new RestructuredText\HTML\Directives\Figure(),
            new RestructuredText\HTML\Directives\Meta(),
            new RestructuredText\HTML\Directives\Stylesheet(),
            new RestructuredText\HTML\Directives\Title(),
            new RestructuredText\HTML\Directives\Url(),
            new RestructuredText\HTML\Directives\Div(),
            new RestructuredText\HTML\Directives\Wrap('note'),
            new RestructuredText\HTML\Directives\ClassDirective(),
        ];
    }

    /**
     * @return NodeRendererFactory[]
     */
    public function getNodeRendererFactories() : array
    {
        $nodeRendererFactories = [
            RestructuredText\Nodes\AnchorNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\AnchorNode $node) {
                    return new RestructuredText\HTML\Renderers\AnchorNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\DefinitionListNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\DefinitionListNode $node) {
                    return new RestructuredText\HTML\Renderers\DefinitionListNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\FigureNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\FigureNode $node) {
                    return new RestructuredText\HTML\Renderers\FigureNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\ImageNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\ImageNode $node) {
                    return new RestructuredText\HTML\Renderers\ImageNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\ListNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\ListNode $node) {
                    return new RestructuredText\Renderers\ListNodeRenderer(
                        $node,
                        new RestructuredText\HTML\Renderers\ListRenderer($node, $this->templateRenderer)
                    );
                }
            ),
            RestructuredText\Nodes\MetaNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\MetaNode $node) {
                    return new RestructuredText\HTML\Renderers\MetaNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\ParagraphNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\ParagraphNode $node) {
                    return new RestructuredText\HTML\Renderers\ParagraphNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\QuoteNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\QuoteNode $node) {
                    return new RestructuredText\HTML\Renderers\QuoteNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\SeparatorNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\SeparatorNode $node) {
                    return new RestructuredText\HTML\Renderers\SeparatorNodeRenderer(
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\TableNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\TableNode $node) {
                    return new RestructuredText\HTML\Renderers\TableNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\TitleNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\TitleNode $node) {
                    return new RestructuredText\HTML\Renderers\TitleNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\TocNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\TocNode $node) {
                    return new RestructuredText\HTML\Renderers\TocNodeRenderer(
                        $node->getEnvironment(),
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\CallableNode::class => new CallableNodeRendererFactory(
                static function (RestructuredText\Nodes\CallableNode $node) {
                    return new RestructuredText\Renderers\CallableNodeRenderer(
                        $node
                    );
                }
            ),
            RestructuredText\Nodes\SectionBeginNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\SectionBeginNode $node) {
                    return new RestructuredText\HTML\Renderers\SectionBeginNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
            RestructuredText\Nodes\SectionEndNode::class => new CallableNodeRendererFactory(
                function (RestructuredText\Nodes\SectionEndNode $node) {
                    return new RestructuredText\HTML\Renderers\SectionEndNodeRenderer(
                        $node,
                        $this->templateRenderer
                    );
                }
            ),
        ];

        $nodeRendererFactories[DocumentNode::class] = new CallableNodeRendererFactory(
            function (DocumentNode $node) {
                return new DocumentNodeRenderer(
                    $node,
                    $this->templateRenderer,
                    $this->subFolder
                );
            }
        );

        $nodeRendererFactories[CodeNode::class] = new CallableNodeRendererFactory(
            function (CodeNode $node) {
                return new RestructuredText\HTML\Renderers\CodeNodeRenderer(
                    $node,
                    $this->templateRenderer,
                    $this->globalTemplatesPath
                );
            }
        );

        $nodeRendererFactories[SpanNode::class] = new CallableNodeRendererFactory(
            function (SpanNode $node) {
                return new RestructuredText\HTML\Renderers\SpanNodeRenderer(
                    $node->getEnvironment(),
                    $node,
                    $this->templateRenderer
                );
            }
        );

        return $nodeRendererFactories;
    }
}
