<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\Directive;

use Doctrine\RST\Directives\SubDirective;
use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Parser;

class SidebarDirective extends SubDirective
{
    public function getName() : string
    {
        return 'sidebar';
    }

    public function processSub(Parser $parser, ?Node $document, string $variable, string $data, array $options) : ?Node
    {
        $wrapperDiv = $parser->renderTemplate(
            'directives/sidebar.html.twig',
            [
                'title' => $data,
            ]
        );

        return $parser->getNodeFactory()->createWrapperNode($document, $wrapperDiv, '</div></div>');
    }
}
