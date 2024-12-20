<?php

declare(strict_types=1);

namespace phpDocumentor\Uml;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\EnumDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
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
    interface "MyInterface" as MyInterface__interface  {
    }
}
namespace phpDocumentor {
     class "MyClass" as MyClass__class    implements \\phpDocumentor\\MyInterface__interface {
    }
}

UML;

        $diagram = new ClassDiagram();
        $this->assertSame(
            $expected,
            $diagram->generateUml(
                [$descriptor, self::faker()->interfaceDescriptor(new Fqsen('\phpDocumentor\MyInterface'))],
            ),
        );
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
     class "MyParent" as MyParent__class    implements \\phpDocumentor\\MyInterface__interface {
    }
}
namespace phpDocumentor {
     class "MyClass" as MyClass__class   extends \\phpDocumentor\\MyParent__class  {
    }
}
namespace phpDocumentor {
    interface "MyInterface" as MyInterface__interface  {
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

    public function testAddsInterfacesToNamespaces(): void
    {
        $namespace = self::faker()->namespaceDescriptor(new Fqsen('\phpDocumentor\MyNamespace'));
        $interface = self::faker()->interfaceDescriptor(new Fqsen('\phpDocumentor\MyNamespace\MyInterface'));
        $interface->setParent(new Collection([
            new Fqsen('\phpDocumentor\MyNamespace\MyParent'),
            self::faker()->interfaceDescriptor(new Fqsen('\phpDocumentor\SecondParent')),
        ]));

        $namespace->setInterfaces(Collection::fromClassString(InterfaceDescriptor::class, [$interface]));

        // @codingStandardsIgnoreStart
        $expected =  <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor\\MyNamespace {
    class "MyParent" as MyParent__class << external >>{
    }
}
namespace phpDocumentor {
    interface "SecondParent" as SecondParent__interface  {
    }
}
namespace phpDocumentor\\MyNamespace {
    interface "MyInterface" as MyInterface__interface  extends \\phpDocumentor\\MyNamespace\\MyParent__class,\\phpDocumentor\\SecondParent__interface {
    }
}

UML;
        // @codingStandardsIgnoreEnd

        $diagram = new ClassDiagram();
        $this->assertSame($expected, $diagram->generateUml([$namespace]));
    }

    public function testAddsEnumsToNamespaces(): void
    {
        $namespace = self::faker()->namespaceDescriptor(new Fqsen('\phpDocumentor\MyNamespace'));
        $enum = self::faker()->enumDescriptor(new Fqsen('\phpDocumentor\MyNamespace\MyEnum'));

        $namespace->setEnums(Collection::fromClassString(EnumDescriptor::class, [$enum]));

        $expected =  <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor\\MyNamespace {
    enum "MyEnum" as MyEnum__enum {
    }
}

UML;

        $diagram = new ClassDiagram();
        $this->assertSame($expected, $diagram->generateUml([$namespace]));
    }

    /** @requires OS Linux */
    public function testAddsTraitsToNamespaces(): void
    {
        $namespace = self::faker()->namespaceDescriptor(new Fqsen('\phpDocumentor\MyNamespace'));
        $classDescriptor = self::faker()->classDescriptor(new Fqsen('\phpDocumentor\MyNamespace\MyClass'));
        $namespace->setClasses(Collection::fromClassString(ClassDescriptor::class, [$classDescriptor]));
        $trait = self::faker()->traitDescriptor(new Fqsen('\phpDocumentor\MyNamespace\MyEnum'));
        $classDescriptor->setUsedTraits(Collection::fromClassString(TraitDescriptor::class, [$trait]));

        $namespace->setTraits(Collection::fromClassString(TraitDescriptor::class, [$trait]));

        $expected =  <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor\\MyNamespace {
    class "MyEnum"  as MyEnum__trait << (T,#FF7700) Trait >> {
    }
}
\\phpDocumentor\\MyNamespace\\MyEnum__trait <-- \\phpDocumentor\\MyNamespace\\MyClass__class : uses
namespace phpDocumentor\\MyNamespace {
     class "MyClass" as MyClass__class    {
    }
}

UML;

        $diagram = new ClassDiagram();
        $this->assertSame($expected, $diagram->generateUml([$namespace]));
    }
}
