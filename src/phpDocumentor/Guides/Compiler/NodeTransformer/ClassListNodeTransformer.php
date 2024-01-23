<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Compiler\NodeTransformer;

use phpDocumentor\Descriptor\Query\Engine;
use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Nodes\CollectionNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\PHP\ClassList;
use phpDocumentor\Guides\Nodes\PHP\DescriptorNode;
use phpDocumentor\Guides\Nodes\SectionNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use Webmozart\Assert\Assert;

use function iterator_to_array;

/** @implements NodeTransformer<ClassList> */
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

        if ($node instanceof ClassList === false) {
            return $node;
        }

        $result = iterator_to_array($this->queryEngine->perform(
            $compilerContext->getVersionDescriptor(),
            $node->getQuery(),
        ));

        foreach ($result as $element) {
            $descriptor = new CollectionNode();
            foreach ($node->getBlueprint() as $bluePrintNode) {
                if ($bluePrintNode instanceof DescriptorNode) {
                    $descriptor->addChildNode($bluePrintNode->withDescriptor($element));
                    continue;
                }

                $descriptor->addChildNode($bluePrintNode);
            }

            if (($descriptor->getChildren()[0] ?? null) instanceof TitleNode) {
                $children = $descriptor->getChildren();
                $descriptor = new SectionNode($children[0]);
                unset($children[0]);
                foreach ($children as $childNode) {
                    $descriptor->addChildNode($childNode);
                }
            }

            $node->addChildNode($descriptor);
        }

        return $node;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof ClassList;
    }

    public function getPriority(): int
    {
        return 6000;
    }
}
