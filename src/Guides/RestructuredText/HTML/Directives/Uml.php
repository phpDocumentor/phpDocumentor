<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Directives;

use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\UmlNode;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Parser;

use function explode;

/**
 * Renders a uml diagram, example:
 *
 * .. uml::
 *    skinparam activityBorderColor #516f42
 *    skinparam activityBackgroundColor #a3dc7f
 *    skinparam shadowing false
 *
 *    start
 *    :Boot the application;
 *    :Parse files into an AST;
 *    :Transform AST into artifacts;
 *    stop
 */
class Uml extends Directive
{
    public function getName(): string
    {
        return 'uml';
    }

    public function process(Parser $parser, ?Node $node, string $variable, string $data, array $options): void
    {
        if ($node instanceof CodeNode === false) {
            return;
        }

        $node = new UmlNode($node->getValue());
        $node->setClasses(explode(' ', $options['classes'] ?? ''));
        $node->setCaption($data);

        if ($variable !== '') {
            $environment = $parser->getEnvironment();
            $environment->setVariable($variable, $node);
        } else {
            $document = $parser->getDocument();
            $document->addNode($node);
        }
    }

    public function wantCode(): bool
    {
        return true;
    }
}
