<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\UmlNode;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;

use function dirname;
use function explode;
use function sprintf;
use function str_replace;

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
final class Uml extends Directive
{
    public function getName(): string
    {
        return 'uml';
    }

    public function process(
        MarkupLanguageParser $parser,
        ?Node $node,
        string $variable,
        string $data,
        array $options
    ): void {
        $environment = $parser->getEnvironment();

        $value = '';
        $caption = '';

        if ($node instanceof CodeNode) {
            $caption = $data;
            $value = $node->getValue();
        }

        if ($node instanceof CodeNode === false && $data) {
            $value = $this->loadExternalUmlFile($environment, $data);
            if ($value === null) {
                return;
            }
        }

        $node = new UmlNode($value);
        $node->setClasses(explode(' ', $options['classes'] ?? ''));
        $node->setCaption($caption);

        if ($variable !== '') {
            $environment->setVariable($variable, $node);
        } else {
            $document = $parser->getDocument();
            $document->addNode($node);
        }
    }

    private function loadExternalUmlFile(Environment $environment, string $path): ?string
    {
        $fileName = sprintf(
            '%s/%s',
            dirname($environment->getCurrentAbsolutePath()),
            $path
        );

        if (!$environment->getOrigin()->has($fileName)) {
            $environment->addError(
                sprintf('Tried to include "%s" as a diagram but the file could not be found', $fileName)
            );

            return null;
        }

        $value = $environment->getOrigin()->read($fileName);

        return str_replace(['@startuml', '@enduml'], '', $value);
    }
}
