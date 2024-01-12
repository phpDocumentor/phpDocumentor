<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\PHP\ElementDescription;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;

final class PHPElementDescription extends BaseDirective
{
    public function getName(): string
    {
        return 'phpdoc:element-description';
    }

    public function getAliases(): array
    {
        return ['phpdoc:description'];
    }

    public function processNode(BlockContext $blockContext, Directive $directive): Node
    {
        return new ElementDescription($directive->getData());
    }
}
