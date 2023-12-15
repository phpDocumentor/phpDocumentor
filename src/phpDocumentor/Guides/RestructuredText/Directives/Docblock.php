<?php

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\DocblockNode;
use phpDocumentor\Guides\Nodes\GenericNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;
use phpDocumentor\Reflection\Fqsen;
use Psr\Log\LoggerInterface;

final class Docblock extends BaseDirective
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function getName(): string
    {
        return 'docblock';
    }

    public function processNode(BlockContext $blockContext, Directive $directive,): Node
    {
        try {
            return new DocblockNode(new Fqsen($directive->getData()));
        } catch (\InvalidArgumentException $e) {
            $this->logger->warning(
                sprintf(
                    'Invalid docblock directive: %s',
                    $e->getMessage()
                )
            );
        }

        return new GenericNode('invalid', $directive->getData());
    }
}
