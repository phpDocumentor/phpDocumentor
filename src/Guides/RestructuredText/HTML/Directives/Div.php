<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\SubDirective;
use phpDocumentor\Guides\RestructuredText\Parser;

/**
 * Divs a sub document in a div with a given class
 */
class Div extends SubDirective
{
    public function getName() : string
    {
        return 'div';
    }

    /**
     * @param string[] $options
     */
    public function processSub(
        Parser $parser,
        ?Node $document,
        string $variable,
        string $data,
        array $options
    ) : ?Node {
        $divOpen = $document->getEnvironment()->getRenderer()->render(
            'div-open.html.twig',
            ['class' => $data]
        );

        return $parser->getNodeFactory()->createWrapperNode($document, $divOpen, '</div>');
    }
}
