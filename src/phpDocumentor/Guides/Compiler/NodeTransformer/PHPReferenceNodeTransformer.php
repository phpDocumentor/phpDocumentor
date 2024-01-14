<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Compiler\NodeTransformer;

use phpDocumentor\Compiler\DescriptorRepository;
use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Nodes\InlineToken\PHPReferenceNode;
use phpDocumentor\Guides\Nodes\Node;

/** @implements NodeTransformer<PHPReferenceNode> */
final class PHPReferenceNodeTransformer implements NodeTransformer
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
        if ($node instanceof PHPReferenceNode === false) {
            return $node;
        }

        $descriptor = $this->descriptorRepository->findDescriptorByTypeAndFqsen(
            $node->getNodeType(),
            $node->getFqsen(),
        );

        return $node->withDescriptor($descriptor);
    }

    public function supports(Node $node): bool
    {
        return $node instanceof PHPReferenceNode;
    }

    public function getPriority(): int
    {
        return 3000;
    }
}
