<?php

declare(strict_types=1);

namespace phpDocumentor\Example\Directives;

use phpDocumentor\Example\Nodes\HelloNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\BaseDirective;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;

final class HelloDirective extends BaseDirective
{
    public function getName(): string
    {
        return 'hello';
    }

    public function processNode(BlockContext $blockContext, Directive $directive): Node
    {
        return new HelloNode($directive->getData());
    }
}
