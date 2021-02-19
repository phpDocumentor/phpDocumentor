<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\UmlNode;
use phpDocumentor\Guides\RestructuredText\Parser;

/**
 * Renders a uml diagram, example:
 *
 * .. uml::
 *
 *
 */
class Uml extends Directive
{
    public function getName(): string
    {
        return 'uml';
    }

    public function process(Parser $parser, ?Node $node, string $variable, string $data, array $options) : void
    {
        $document = $parser->getDocument();

        if ($node instanceof CodeNode) {
            $node = new UmlNode($node->getValue());
        }

        $document->addNode($node);
    }

    public function wantCode(): bool
    {
        return true;
    }
}
