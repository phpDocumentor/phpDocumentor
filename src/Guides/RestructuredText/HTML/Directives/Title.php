<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Parser;

/**
 * Add a meta title to the document
 *
 * .. title:: Page title
 */
class Title extends Directive
{
    public function getName() : string
    {
        return 'title';
    }

    /**
     * @param string[] $options
     */
    public function process(
        Parser $parser,
        ?Node $node,
        string $variable,
        string $data,
        array $options
    ) : void {
        $document = $parser->getDocument();

        $environment = $node->getEnvironment();
        $title = static function () use ($environment, $data) {
            return $environment->getRenderer()->render('title.html.twig', ['title' => $data]);
        };

        $document->addHeaderNode(
            $parser->getNodeFactory()->createRawNode($title)
        );

        if ($node === null) {
            return;
        }

        $document->addNode($node);
    }
}
