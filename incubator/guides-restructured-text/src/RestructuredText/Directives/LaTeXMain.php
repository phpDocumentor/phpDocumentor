<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\MainNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;

/**
 * Marks the document as LaTeX main
 */
class LaTeXMain extends Directive
{
    public function getName(): string
    {
        return 'latex-main';
    }

    /**
     * @param string[] $options
     */
    public function processNode(
        MarkupLanguageParser $parser,
        string $variable,
        string $data,
        array $options
    ): Node {
        return new MainNode();
    }
}
