<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\PHPClassList as PHPClassListNode;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;

final class PHPClassList extends BaseDirective
{
    public function getName(): string
    {
        return 'php-class-list';
    }

    public function processNode(BlockContext $blockContext, Directive $directive): Node
    {
        return new PHPClassListNode($directive->getData());
    }
}
