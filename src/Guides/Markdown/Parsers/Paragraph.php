<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Markdown\Parsers;

use League\CommonMark\Block\Element\Paragraph as CommonMarkParagraph;
use League\CommonMark\Node\NodeWalker;
use League\CommonMark\Node\NodeWalkerEvent;
use phpDocumentor\Guides\Nodes;
use phpDocumentor\Guides\Parser;
use phpDocumentor\Guides\RestructuredText\NodeFactory\DefaultNodeFactory;
use function assert;
use function get_class;

final class Paragraph extends AbstractBlock
{
    public function __construct(Nodes\Factory $nodeFactory)
    {
        $this->nodeFactory = $nodeFactory;
    }

    /**
     * @return Nodes\ParagraphNode
     */
    public function parse(Parser $parser, NodeWalker $walker) : Nodes\Node
    {
        $nodeFactory = $this->nodeFactory;
        assert($nodeFactory instanceof DefaultNodeFactory);

        $context = $nodeFactory->createParagraphNode($nodeFactory->createSpanNode($parser, ''));

        while ($event = $walker->next()) {
            $node = $event->getNode();

            if ($event->isEntering()) {
                continue;
            }

            if ($node instanceof CommonMarkParagraph) {
                return $context;
            }

            echo 'PARAGRAPH CONTEXT: I am '
                . 'leaving'
                . ' a '
                . get_class($node)
                . ' node'
                . "\n";
        }

        return $context;
    }

    public function supports(NodeWalkerEvent $event) : bool
    {
        return $event->isEntering() && $event->getNode() instanceof CommonMarkParagraph;
    }
}
