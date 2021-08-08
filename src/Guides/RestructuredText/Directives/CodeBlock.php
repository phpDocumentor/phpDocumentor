<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser;

use function trim;

/**
 * Renders a code block, example:
 *
 * .. code-block:: php
 *
 *      <?php
 *
 *      echo "Hello world!\n";
 *
 * @link https://www.sphinx-doc.org/en/master/usage/restructuredtext/directives.html#directive-code-block
 */
class CodeBlock extends Directive
{
    public function getName(): string
    {
        return 'code-block';
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
    ): void {
        if ($node === null) {
            return;
        }

        if ($node instanceof CodeNode) {
            $node->setLanguage(trim($data));
            $this->setStartingLineNumberBasedOnOptions($options, $node);
        }

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

    /**
     * @param string[] $options
     */
    private function setStartingLineNumberBasedOnOptions(array $options, CodeNode $node): void
    {
        $startingLineNumber = null;
        if (isset($options['linenos'])) {
            $startingLineNumber = 1;
        }

        $startingLineNumber = $options['number-lines'] ?? $options['lineno-start'] ?? $startingLineNumber;

        if ($startingLineNumber === null) {
            return;
        }

        $node->setStartingLineNumber((int) $startingLineNumber);
    }
}
