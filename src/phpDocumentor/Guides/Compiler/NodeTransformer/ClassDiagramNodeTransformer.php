<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Compiler\NodeTransformer;

use phpDocumentor\Descriptor\Query\Engine;
use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Graphs\Nodes\UmlNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\PHP\ClassDiagram;
use phpDocumentor\Uml\ClassDiagram as UmlDiagram;
use Webmozart\Assert\Assert;

/** @implements NodeTransformer<ClassDiagram|UmlNode> */
final class ClassDiagramNodeTransformer implements NodeTransformer
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
        $elements = $this->queryEngine->perform(
            $compilerContext->getVersionDescriptor(),
            $node->getQuery(),
        );

        $classDiagram = new UmlDiagram();
        $umlNode = new UmlNode($classDiagram->generateUml($elements));
        $umlNode->setCaption($node->getCaption());

        return $umlNode;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof ClassDiagram;
    }

    public function getPriority(): int
    {
        return 6000;
    }
}
