<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Compiler\NodeTransformer;

use phpDocumentor\Compiler\DescriptorRepository;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\InlineToken\PHPReferenceNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\SpanNode;

/** @implements NodeTransformer<SpanNode> */
final class PHPReferenceNodeTransformer implements NodeTransformer
{
    private DescriptorRepository $descriptorRepository;

    public function __construct(DescriptorRepository $descriptorRepository)
    {
        $this->descriptorRepository = $descriptorRepository;
    }

    public function enterNode(Node $node, DocumentNode $documentNode): Node
    {
        return $node;
    }

    public function leaveNode(Node $node, DocumentNode $documentNode): Node|null
    {
        if ($node instanceof SpanNode === false) {
            return $node;
        }

        foreach ($node->getTokens() as $token) {
            if (!($token instanceof PHPReferenceNode)) {
                continue;
            }

            $descriptor = $this->descriptorRepository->findDescriptorByTypeAndFqsen(
                $token->getNodeType(),
                $token->getFqsen()
            );
            $token->setDescriptor($descriptor);
        }

        return $node;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof SpanNode;
    }

    public function getPriority(): int
    {
        return 3000;
    }
}
