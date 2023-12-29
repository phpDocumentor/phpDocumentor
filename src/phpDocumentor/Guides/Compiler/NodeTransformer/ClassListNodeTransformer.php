<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Compiler\NodeTransformer;

use phpDocumentor\Descriptor\Query\Engine;
use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\PHPClassList;
use Webmozart\Assert\Assert;

use function iterator_to_array;

/** @implements NodeTransformer<PHPClassList> */
final class ClassListNodeTransformer implements NodeTransformer
{
    public function __construct(private readonly Engine $queryEngine)
    {
    }

    public function enterNode(Node $node, CompilerContext $compilerContext): Node
    {
        return $node;
    }

    public function leaveNode(Node $node, CompilerContext $compilerContext): Node|null
    {
        Assert::isInstanceOf($compilerContext, DescriptorAwareCompilerContext::class);

        if ($node instanceof PHPClassList === false) {
            return $node;
        }

        $result = iterator_to_array($this->queryEngine->perform(
            $compilerContext->getVersionDescriptor(),
            $node->getQuery(),
        ));

        $node->setValue($result);

        return $node;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof PHPClassList;
    }

    public function getPriority(): int
    {
        return 3000;
    }
}
