<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Nodes\ContainerNode;

/**
 * Divs a sub document in a div with a given class or set of classes.
 *
 * @link https://docutils.sourceforge.io/docs/ref/rst/directives.html#container
 */
class ContainerDirective extends SubDirective
{
    public function getName(): string
    {
        return 'container';
    }

    public function getAliases(): array
    {
        return ['div'];
    }

    /**
     * @param string[] $options
     */
    public function processSub(
        MarkupLanguageParser $parser,
        ?Node $document,
        string $variable,
        string $data,
        array $options
    ): ?Node {
        return (new ContainerNode($document))->withOptions(['class' => $data]);
    }
}
