<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\PHP\ElementName;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;

final class PHPElementName extends BaseDirective
{
    public function getName(): string
    {
        return 'phpdoc:element-name';
    }

    public function getAliases(): array
    {
        return ['phpdoc:name'];
    }

    public function processNode(BlockContext $blockContext, Directive $directive): Node
    {
        return new ElementName($directive->getData());
    }
}
