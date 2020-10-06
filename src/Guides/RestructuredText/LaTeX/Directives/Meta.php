<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\LaTeX\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Parser;

/**
 * Add a meta information:
 *
 * .. meta::
 *      :key: value
 */
class Meta extends Directive
{
    public function getName() : string
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
    ) : void {
        $document = $parser->getDocument();

        $nodeFactory = $parser->getNodeFactory();

        foreach ($options as $key => $value) {
            $meta = $nodeFactory->createMetaNode($key, $value);

            $document->addHeaderNode($meta);
        }

        if ($node === null) {
            return;
        }

        $document->addNode($node);
    }
}
