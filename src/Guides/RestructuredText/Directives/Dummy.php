<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser;

class Dummy extends Directive
{
    public function getName() : string
    {
        return 'dummy';
    }

    /**
     * @param string[] $options
     */
    public function processNode(
        Parser $parser,
        string $variable,
        string $data,
        array $options
    ) : ?Node {
        return $parser->getNodeFactory()->createDummyNode(
            [
                'data' => $data,
                'options' => $options,
            ]
        );
    }
}
