<?php

declare(strict_types=1);

namespace phpDocumentor\Uml;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\EnumDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
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
    public function generateUml(iterable $descriptors): string
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
        if ($class instanceof Fqsen) {
            $this->fqsen($class, stereoType: '<< external >>');

            return;
        }

        $classFqsen = $class->getFullyQualifiedStructuralElementName();
        $reference = $classFqsen . '__class';

        if (isset($this->elements[$reference]) && $this->elements[$reference]->descriptorBased === true) {
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
            if ($parent instanceof InterfaceInterface) {
                $this->interfaceDescriptor($parent);
                $implementsList[] = addslashes($parent->getFullyQualifiedStructuralElementName() . '__interface');
                continue;
            }

            $this->fqsen($parent, 'interface', '<< external >>');
            $implementsList[] = addslashes($parent . '__class');
        }

        if ($implementsList !== []) {
            $implements = ' implements ' . implode(',', $implementsList);
        } else {
            $implements = '';
        }

        foreach ($class->getUsedTraits() as $trait) {
            if ($trait instanceof TraitInterface) {
                $this->traitDescriptor($trait);
                $output .= addslashes($trait->getFullyQualifiedStructuralElementName() . '__trait')
                        . ' <-- ' . addslashes(
                            $this->toClassName((string) $class->getFullyQualifiedStructuralElementName()),
                        )
                    . ' : uses' . PHP_EOL;
                continue;
            }

            $output .= addslashes($this->toClassName((string) $trait)) . ' <-- ' . addslashes(
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

    private function interfaceDescriptor(InterfaceInterface|Fqsen $descriptor): void
    {
        if ($descriptor instanceof Fqsen) {
            $this->fqsen($descriptor, 'interface', '<< external >>');

            return;
        }

        $fqsen = $descriptor->getFullyQualifiedStructuralElementName();

        $extends = '';
        $parents = [];

        foreach ($descriptor->getParent() as $parent) {
            $this->interfaceDescriptor($parent);
            if ($parent instanceof InterfaceInterface) {
                $parents[] = addslashes($parent->getFullyQualifiedStructuralElementName() . '__interface');
                continue;
            }

            $parents[] = addslashes($parent . '__class');
        }

        if ($parents !== []) {
            $extends = ' extends ' . implode(',', $parents);
        }

        $namespace = addslashes($this->getNamespace($fqsen));
        $className = $descriptor->getName();
        $alias = $className . '__interface';
        $reference = $fqsen . '__interface';

        $output = <<<PUML
namespace $namespace {
    interface "$className" as $alias $extends {
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
        $this->createUmlElements($descriptor->getInterfaces()->getAll());
        $this->createUmlElements($descriptor->getEnums()->getAll());
        $this->createUmlElements($descriptor->getTraits()->getAll());
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
    private function createUmlElements(iterable $descriptors): void
    {
        foreach ($descriptors as $descriptor) {
            match (true) {
                $descriptor instanceof ClassInterface => $this->classDescriptor($descriptor),
                $descriptor instanceof InterfaceDescriptor => $this->interfaceDescriptor($descriptor),
                $descriptor instanceof EnumDescriptor => $this->enumDescriptor($descriptor),
                $descriptor instanceof TraitDescriptor => $this->traitDescriptor($descriptor),
                $descriptor instanceof NamespaceInterface => $this->namespaceDescriptor($descriptor),
                $descriptor instanceof Fqsen => $this->fqsen($descriptor, stereoType: '<< external >>'),
                default => null,
            };
        }
    }

    private function fqsen(Fqsen $fqsen, string $type = 'class', string|null $stereoType = null): void
    {
        $namespaceName = addslashes($this->getNamespace($fqsen));
        $pclassName = $fqsen->getName();
        $alias = $pclassName . '__class';
        $reference = $fqsen . '__class';

        if (isset($this->elements[$reference])) {
            return;
        }

        if ($namespaceName === '') {
            $this->elements[$reference] = new Element(
                <<<PUML
$type "$pclassName" as $alias $stereoType{
}

PUML,
                false,
            );

            return;
        }

        $this->elements[$reference] = new Element(
            <<<PUML
namespace {$namespaceName} {
    class "$pclassName" as $alias $stereoType{
    }
}

PUML,
            false,
        );
    }

    private function enumDescriptor(EnumDescriptor $descriptor): void
    {
        $fqsen = $descriptor->getFullyQualifiedStructuralElementName();
        $namespace = addslashes($this->getNamespace($fqsen));
        $className = $descriptor->getName();
        $alias = $className . '__enum';
        $reference = $fqsen . '__enum';

        $output = <<<PUML
namespace $namespace {
    enum "$className" as $alias {
    }
}

PUML;

        $this->elements[$reference] = new Element($output, true);
    }

    private function traitDescriptor(TraitInterface $trait): void
    {
        $fqsen = $trait->getFullyQualifiedStructuralElementName();
        $namespace = addslashes($this->getNamespace($fqsen));
        $className = $trait->getName();
        $alias = $className . '__trait';
        $reference = $fqsen . '__trait';

        $output = <<<PUML
namespace $namespace {
    class "$className"  as $alias << (T,#FF7700) Trait >> {
    }
}

PUML;

        $this->elements[$reference] = new Element($output, true);
    }
}
