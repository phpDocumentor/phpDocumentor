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

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\RestructuredText\Nodes\SidebarNode;

/**
 * Divs a sub document in a div with a given class or set of classes.
 *
 * https://docutils.sourceforge.io/docs/ref/rst/directives.html#sidebar
 */
class SidebarDirective extends SubDirective
{
    public function getName(): string
    {
        return 'sidebar';
    }

    public function processSub(
        MarkupLanguageParser $parser,
        ?Node $document,
        string $variable,
        string $data,
        array $options
    ): ?Node {
        return (new SidebarNode(
            $data,
            $document
        ))->withOptions($options);
    }
}
