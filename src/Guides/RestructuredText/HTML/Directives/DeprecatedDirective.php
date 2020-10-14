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

class DeprecatedDirective extends SubDirective
{
    public function getName() : string
    {
        return 'deprecated';
    }

    public function processSub(Parser $parser, ?Node $document, string $variable, string $data, array $options) : ?Node
    {
        $wrapperDiv = $document->getEnvironment()->getRenderer()->render(
            'directives/deprecated.html.twig',
            ['version' => $data]
        );

        return $parser->getNodeFactory()->createWrapperNode($document, $wrapperDiv, '</div></div>');
    }
}
