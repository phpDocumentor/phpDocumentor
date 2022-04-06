<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;

/**
 * Renders a raw block, example:
 *
 * .. raw::
 *
 *      <u>Underlined!</u>
 *
 * @link https://docutils.sourceforge.io/docs/ref/rst/directives.html#raw-data-pass-through
 */
class RawDirective extends Directive
{
    public function getName(): string
    {
        return 'raw';
    }

    /**
     * @param string[] $options
     */
    public function process(
        MarkupLanguageParser $parser,
        ?Node $node,
        string $variable,
        string $data,
        array $options
    ): void {
        if ($node === null) {
            return;
        }

        $document = $parser->getDocument();
        if ($variable !== '') {
            $document->addVariable($variable, $node);
        } else {
            $document->addNode($node);
        }
    }
}
