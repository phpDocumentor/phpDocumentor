<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\PHP\ClassDiagram as ClassDiagramNode;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;

final class ClassDiagram extends BaseDirective
{
    public function getName(): string
    {
        return 'phpdoc:class-diagram';
    }

    public function processNode(BlockContext $blockContext, Directive $directive): ClassDiagramNode
    {
        return (new ClassDiagramNode($directive->getData()))
            ->setCaption((string) $directive->getOption('caption')->getValue());
    }
}
