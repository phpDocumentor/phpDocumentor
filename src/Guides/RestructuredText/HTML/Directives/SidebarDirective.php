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

class SidebarDirective extends SubDirective
{
    public function getName() : string
    {
        return 'sidebar';
    }

    public function processSub(Parser $parser, ?Node $document, string $variable, string $data, array $options) : ?Node
    {
        $environment = $document->getEnvironment();
        $wrapperDiv = static function () use ($environment, $data) {
            $environment->getRenderer()->render(
                'directives/sidebar.html.twig',
                ['title' => $data]
            );
        };

        return $parser->getNodeFactory()->createWrapperNode($document, $wrapperDiv, '</div></div>');
    }
}
