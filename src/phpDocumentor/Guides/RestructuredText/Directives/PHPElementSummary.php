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
use phpDocumentor\Guides\Nodes\PHP\ElementSummary;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;

final class PHPElementSummary extends BaseDirective
{
    public function getName(): string
    {
        return 'phpdoc:element-summary';
    }

    public function getAliases(): array
    {
        return ['phpdoc:summary'];
    }

    public function processNode(BlockContext $blockContext, Directive $directive): Node
    {
        return new ElementSummary($directive->getData());
    }
}
