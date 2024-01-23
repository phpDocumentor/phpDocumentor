<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Compiler\NodeTransformer;

use Generator;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Query\Engine;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Guides\Compiler\CompilerContext;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Graphs\Nodes\UmlNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Nodes\PHP\ClassDiagram;
use phpDocumentor\Reflection\Fqsen;
use Webmozart\Assert\Assert;

use function addslashes;
use function array_pop;
use function explode;
use function implode;
use function trim;

use const PHP_EOL;

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

        $uml = <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\


UML;

        foreach ($elements as $element) {
            if (! ($element instanceof ClassInterface)) {
                continue;
            }

            foreach ($this->classDescriptor($element) as $elementUml) {
                $uml .= $elementUml;
            }
        }

        $umlNode = new UmlNode($uml);
        $umlNode->setCaption($node->getCaption());

        return $umlNode;
    }

    private function classDescriptor(ClassInterface $class): Generator
    {
        $output = '';
        $abstract = $class->isAbstract() ? 'abstract ' : '';
        $realClassName = $class->getName();
        $namespace = addslashes($this->getNamespace($class->getFullyQualifiedStructuralElementName()));
        $className = $class->getName() . '__class ';

        $extends = '';
        if ($class->getParent() !== null) {
            $parentFqsen = $class->getParent() instanceof ClassDescriptor
                ? $class->getParent()->getFullyQualifiedStructuralElementName()
                : $class->getParent();

            $pnamespace = addslashes($this->getNamespace($parentFqsen));
            $pclassName = $parentFqsen->getName();
            $alias = $pclassName . '__class';
            if ($pnamespace === '') {
                yield <<<PUML
class "$pclassName" as $alias {
}

PUML;
            } else {
                yield <<<PUML
namespace {$pnamespace} {
    class "$pclassName" as $alias {
    }
}

PUML;
            }

            $extends = ' extends ' . addslashes($this->toClassName((string) $parentFqsen));
        }

        $implementsList = [];
        foreach ($class->getInterfaces() as $parent) {
            $parentFqsen = $parent instanceof InterfaceDescriptor
                ? (string) $parent->getFullyQualifiedStructuralElementName()
                : (string) $parent;

            $implementsList[] = addslashes($parentFqsen);
        }

        if ($implementsList !== []) {
            $implements = ' implements ' . implode(',', $implementsList);
        } else {
            $implements = '';
        }

        foreach ($class->getUsedTraits() as $parent) {
            $parentFqsen = $parent instanceof TraitDescriptor
                ? (string) $parent->getFullyQualifiedStructuralElementName()
                : (string) $parent;

            $output .= addslashes($parentFqsen) . ' <-- ' . addslashes(
                $this->toClassName((string) $class->getFullyQualifiedStructuralElementName()),
            ) . ' : uses' . PHP_EOL;
        }

        $output .= <<<PUML
namespace $namespace {
    $abstract class "$realClassName" as $className $extends $implements {
    }
}

PUML;

        yield $output;
    }

    public function supports(Node $node): bool
    {
        return $node instanceof ClassDiagram;
    }

    public function getPriority(): int
    {
        return 6000;
    }

    private function toClassName(string $parentFqsen): string
    {
        return $parentFqsen . '__class';
    }

    private function getNamespace(Fqsen $fqsen): string
    {
        $parts = explode('\\', (string) $fqsen);
        array_pop($parts);

        return trim(implode('\\', $parts), '\\');
    }
}
