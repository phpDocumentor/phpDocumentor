<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\RestructuredText\HTML\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\SubDirective;
use phpDocumentor\Guides\RestructuredText\Parser;

class TopicDirective extends SubDirective
{
    final public function processSub(
        Parser $parser,
        ?Node $document,
        string $variable,
        string $data,
        array $options
    ) : ?Node {
        $wrapperDiv = $document->getEnvironment()->getRenderer()->render(
            'directives/topic.html.twig',
            ['name' => $data]
        );

        return $parser->getNodeFactory()->createWrapperNode($document, $wrapperDiv, '</div>');
    }

    public function getName() : string
    {
        return 'topic';
    }
}
