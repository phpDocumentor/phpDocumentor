<?php

namespace phpDocumentor\Guides\Compiler\NodeTransformer;

use phpDocumentor\Compiler\DescriptorRepository;
use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Nodes\DocblockNode;
use phpDocumentor\Guides\Nodes\Node;

class DocblockNodeTransformer implements NodeTransformer
{
    public function __construct(private readonly DescriptorRepository $descriptorRepository)
    {
    }

    public function enterNode(Node $node, CompilerContext $compilerContext): Node
    {
        return $node;
    }

    public function leaveNode(Node $node, CompilerContext $compilerContext): Node|null
    {
        if ($node instanceof DocblockNode === false) {
            return $node;
        }

        $descriptor = $this->descriptorRepository->findDescriptorByFqsen(
            $node->getFqsen(),
        );

        return $node->withDescriptor($descriptor);
    }

    public function supports(Node $node): bool
    {
        return $node instanceof DocblockNode;
    }

    public function getPriority(): int
    {
        return 3000;
    }
}
