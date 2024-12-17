<?php

declare(strict_types=1);

namespace phpDocumentor\Uml;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Uml\ClassDiagram\Element;

use function addslashes;
use function array_pop;
use function explode;
use function implode;
use function trim;

use const PHP_EOL;

class ClassDiagram
{
    /** @var array<string, Element> */
    private array $elements = [];

    /** @param ElementInterface[] $descriptors */
    public function generateUml(array $descriptors): string
    {
        $uml = <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\


UML;

        $this->createUmlElements($descriptors);

        foreach ($this->elements as $element) {
            $uml .= $element->uml;
        }

        return $uml;
    }

    private function classDescriptor(ClassInterface|Fqsen $class): void
    {
        $classFqsen = $class instanceof ClassDescriptor
            ? $class->getFullyQualifiedStructuralElementName()
            : $class;

        $pnamespace = addslashes($this->getNamespace($classFqsen));
        $pclassName = $class->getName();
        $alias = $pclassName . '__class';
        $reference = $classFqsen . '__class';

        if (isset($this->elements[$reference]) && $this->elements[$reference]->descriptorBased === true) {
            return;
        }

        if ($class instanceof Fqsen) {
            if (isset($this->elements[$reference])) {
                return;
            }

            if ($pnamespace === '') {
                $this->elements[$reference] = new Element(
                    <<<PUML
class "$pclassName" as $alias {
}

PUML,
                    false,
                );

                return;
            }

            $this->elements[$reference] = new Element(
                <<<PUML
namespace {$pnamespace} {
    class "$pclassName" as $alias {
    }
}

PUML,
                false,
            );

            return;
        }

        $output = '';
        $abstract = $class->isAbstract() ? 'abstract ' : '';
        $realClassName = $class->getName();
        $namespace = addslashes($this->getNamespace($class->getFullyQualifiedStructuralElementName()));
        $className = $class->getName() . '__class ';

        $extends = '';
        if ($class->getParent() instanceof ClassDescriptor || $class->getParent() instanceof Fqsen) {
            $this->classDescriptor($class->getParent());
            $parentFqsen = $class->getParent() instanceof ClassDescriptor
                ? $class->getParent()->getFullyQualifiedStructuralElementName()
                : $class->getParent();

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

        $this->elements[$reference] = new Element($output, true);
    }

    private function namespaceDescriptor(NamespaceInterface $descriptor): void
    {
        if ($descriptor->isEmpty()) {
            return;
        }

        $this->createUmlElements($descriptor->getClasses()->getAll());
        foreach ($descriptor->getChildren() as $child) {
            $this->namespaceDescriptor($child);
        }
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

    /** @param ElementInterface[] $descriptors */
    private function createUmlElements(array $descriptors): void
    {
        foreach ($descriptors as $descriptor) {
            match (true) {
                $descriptor instanceof ClassInterface => $this->classDescriptor($descriptor),
                $descriptor instanceof NamespaceInterface => $this->namespaceDescriptor($descriptor),
                default => null,
            };
        }
    }
}
