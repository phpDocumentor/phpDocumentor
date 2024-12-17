<?php

declare(strict_types=1);

namespace phpDocumentor\Uml;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

class ClassDiagramTest extends TestCase
{
    use Faker;

    public function testGenerateSimpleClassDiagram(): void
    {
        $descriptor = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyClass'));

        $expected =  <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor {
     class "MyClass" as MyClass__class    {
    }
}

UML;

        $diagram = new ClassDiagram();
        $this->assertSame($expected, $diagram->generateUml([$descriptor]));
    }

    public function testGenerateClassWithInterfaces(): void
    {
        $descriptor = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyClass'));
        $descriptor->setInterfaces(Collection::fromClassString(
            InterfaceDescriptor::class,
            [
                self::faker()->interfaceDescriptor(new Fqsen('\phpDocumentor\MyInterface')),
            ],
        ));

        $expected =  <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor {
     class "MyClass" as MyClass__class    implements \\phpDocumentor\\MyInterface {
    }
}

UML;

        $diagram = new ClassDiagram();
        $this->assertSame($expected, $diagram->generateUml([$descriptor]));
    }

    public function testGenerateClassWithParentInSubNamespace(): void
    {
        $descriptor = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyClass'));
        $descriptor->setParent(self::faker()->classDescriptor(new Fqsen('\phpDocumentor\SubNamespace\MyParent')));

        $expected =  <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor\\SubNamespace {
     class "MyParent" as MyParent__class    {
    }
}
namespace phpDocumentor {
     class "MyClass" as MyClass__class   extends \\phpDocumentor\\SubNamespace\\MyParent__class  {
    }
}

UML;

        $diagram = new ClassDiagram();
        $this->assertSame($expected, $diagram->generateUml([$descriptor]));
    }

    public function testGenerateClassWithParentSame(): void
    {
        $descriptor = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyClass'));
        $descriptor->setParent(self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyParent')));

        $expected =  <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor {
     class "MyParent" as MyParent__class    {
    }
}
namespace phpDocumentor {
     class "MyClass" as MyClass__class   extends \\phpDocumentor\\MyParent__class  {
    }
}

UML;

        $diagram = new ClassDiagram();
        $this->assertSame($expected, $diagram->generateUml([$descriptor]));
    }

    public function testGenerateMultipleClasses(): void
    {
        $descriptor1 = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyClass'));
        $descriptor2 = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MySecondClass'));
        $expected =  <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor {
     class "MyClass" as MyClass__class    {
    }
}
namespace phpDocumentor {
     class "MySecondClass" as MySecondClass__class    {
    }
}

UML;

        $diagram = new ClassDiagram();
        $this->assertSame($expected, $diagram->generateUml([$descriptor1, $descriptor2]));
    }

    public function testGenerateMultipleClassesWithParent(): void
    {
        $descriptor1 = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyClass'));
        $descriptor2 = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyParent'));
        $descriptor2->setInterfaces(Collection::fromClassString(
            InterfaceDescriptor::class,
            [
                self::faker()->interfaceDescriptor(new Fqsen('\phpDocumentor\MyInterface')),
            ],
        ));
        $descriptor3 = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MySecondClass'));
        $descriptor3->setParent($descriptor2);
        $descriptor1->setParent(new Fqsen('\phpDocumentor\MyParent'));
        $expected =  <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor {
     class "MyParent" as MyParent__class    implements \\phpDocumentor\\MyInterface {
    }
}
namespace phpDocumentor {
     class "MyClass" as MyClass__class   extends \\phpDocumentor\\MyParent__class  {
    }
}
namespace phpDocumentor {
     class "MySecondClass" as MySecondClass__class   extends \\phpDocumentor\\MyParent__class  {
    }
}

UML;

        $diagram = new ClassDiagram();
        $this->assertSame($expected, $diagram->generateUml([$descriptor1, $descriptor3, $descriptor2]));
    }

    public function testGenerateClassAndNamspaceDescriptor(): void
    {
        $descriptor1 = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyClass'));
        $descriptor2 = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyNamespace\MyClass'));

        $namespace = self::faker()->namespaceDescriptor(new Fqsen('\phpDocumentor\MyNamespace'));
        $namespace->setClasses(
            Collection::fromClassString(ClassDescriptor::class, [
                $descriptor2,
                self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyNamespace\MySecondClass')),

            ]),
        );

        $expected =  <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor {
     class "MyClass" as MyClass__class    {
    }
}
namespace phpDocumentor\\MyNamespace {
     class "MyClass" as MyClass__class    {
    }
}
namespace phpDocumentor\\MyNamespace {
     class "MySecondClass" as MySecondClass__class    {
    }
}

UML;

        $diagram = new ClassDiagram();
        $this->assertSame($expected, $diagram->generateUml([$descriptor1, $descriptor2, $namespace]));
    }
}
