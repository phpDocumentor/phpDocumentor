<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\MetaNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser;

/**
 * Add a meta information:
 *
 * .. meta::
 *      :key: value
 */
class Meta extends Directive
{
    public function getName(): string
    {
        return 'meta';
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
        $document = $parser->getDocument();

        foreach ($options as $key => $value) {
            $document->addHeaderNode(new MetaNode($key, $value));
        }

        if ($node === null) {
            return;
        }

        $document->addNode($node);
    }
}
