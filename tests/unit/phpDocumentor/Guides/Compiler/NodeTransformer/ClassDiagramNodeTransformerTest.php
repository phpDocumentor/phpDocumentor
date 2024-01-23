<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Compiler\NodeTransformer;

use Generator;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\Query\Engine;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Graphs\Nodes\UmlNode;
use phpDocumentor\Guides\Nodes\PHP\ClassDiagram;
use phpDocumentor\Guides\Nodes\ProjectNode;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class ClassDiagramNodeTransformerTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    /** @dataProvider simpleClassProvider */
    public function testSimpleClassTransformation(array $queryResult, string $uml): void
    {
        $queryEngine = $this->prophesize(Engine::class);
        $queryEngine->perform(
            Argument::type(VersionDescriptor::class),
            Argument::any(),
        )->willYield($queryResult);

        $transformer = new ClassDiagramNodeTransformer($queryEngine->reveal());
        $result = $transformer->leaveNode(
            new ClassDiagram('ignore'),
            new DescriptorAwareCompilerContext(
                new ProjectNode(),
                new VersionDescriptor('1', Collection::fromClassString(ClassDescriptor::class)),
            ),
        );

        self::assertInstanceOf(UmlNode::class, $result);
        self::assertEquals(
            $uml,
            $result->getValue(),
        );
    }

    public function simpleClassProvider(): Generator
    {
        yield 'simple class' => [
            'queryResult' => [
                $this->faker()->classDescriptor(new Fqsen('\phpDocumentor\MyClass')),
            ],
            'uml' =>             <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor {
     class "MyClass" as MyClass__class    {
    }
}

UML,
        ];

        $class = $this->faker()->classDescriptor(new Fqsen('\phpDocumentor\MyClass'));
        $class->setInterfaces(Collection::fromClassString(
            InterfaceDescriptor::class,
            [
                $this->faker()->interfaceDescriptor(new Fqsen('\phpDocumentor\MyInterface')),
            ],
        ));

        yield 'simple class with interface' => [
            'queryResult' => [$class],
            'uml' =>             <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor {
     class "MyClass" as MyClass__class    implements \\phpDocumentor\\MyInterface {
    }
}

UML,
        ];

        $class = $this->faker()->classDescriptor(new Fqsen('\phpDocumentor\MyClass'));
        $class->setParent($this->faker()->classDescriptor(new Fqsen('\phpDocumentor\SubNamespace\MyParent')));

        yield 'simple class with parent' => [
            'queryResult' => [$class],
            'uml' =>             <<<'UML'
skinparam shadowing false
skinparam linetype ortho
hide empty members
left to right direction
set namespaceSeparator \\

namespace phpDocumentor\\SubNamespace {
    class "MyParent" as MyParent__class {
    }
}
namespace phpDocumentor {
     class "MyClass" as MyClass__class   extends \\phpDocumentor\\SubNamespace\\MyParent__class  {
    }
}

UML,
        ];
    }
}
