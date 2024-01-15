<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Compiler\NodeTransformer;

use ArrayIterator;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Interfaces\ElementInterface;
use phpDocumentor\Descriptor\Query\Engine;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Nodes\CollectionNode;
use phpDocumentor\Guides\Nodes\InlineCompoundNode;
use phpDocumentor\Guides\Nodes\InlineToken\PHPReferenceNode;
use phpDocumentor\Guides\Nodes\PHP\ClassList;
use phpDocumentor\Guides\Nodes\PHP\ElementName;
use phpDocumentor\Guides\Nodes\ProjectNode;
use phpDocumentor\Guides\Nodes\SectionNode;
use phpDocumentor\Guides\Nodes\TitleNode;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class ClassListNodeTransformerTest extends TestCase
{
    use ProphecyTrait;

    public function testQueryResultExpandsClassList(): void
    {
        $descriptor1 = $this->createDescriptor('\MyTestClass');
        $descriptor2 = $this->createDescriptor('\MyOtherClass');
        $engine = $this->prophesize(Engine::class);
        $engine->perform(
            Argument::type(VersionDescriptor::class),
            Argument::containingString('class-list'),
        )->willReturn(
            new ArrayIterator(
                [
                    $descriptor1,
                    $descriptor2,
                ],
            ),
        );

        $node = new ClassList(
            [new ElementName('')],
            'class-list',
        );

        $context = new DescriptorAwareCompilerContext(
            new ProjectNode(),
            new VersionDescriptor('1.0.0', new Collection()),
        );

        $transformer = new ClassListNodeTransformer($engine->reveal());
        $result = $transformer->leaveNode($node, $context);

        $this->assertEquals(
            [
                new CollectionNode([
                    (new PHPReferenceNode('class', new Fqsen('\MyTestClass')))
                        ->withDescriptor($descriptor1),
                ]),
                new CollectionNode([
                    (new PHPReferenceNode('class', new Fqsen('\MyOtherClass')))
                        ->withDescriptor($descriptor2),
                ]),
            ],
            $result->getChildren(),
        );
    }

    public function testQueryResultExpandsBluePrintToSectionsWhenFirstElementIsTitle(): void
    {
        $descriptor1 = $this->createDescriptor('\MyTestClass');
        $descriptor2 = $this->createDescriptor('\MyOtherClass');
        $engine = $this->prophesize(Engine::class);
        $engine->perform(
            Argument::type(VersionDescriptor::class),
            Argument::containingString('class-list'),
        )->willReturn(
            new ArrayIterator(
                [
                    $descriptor1,
                    $descriptor2,
                ],
            ),
        );

        $node = new ClassList(
            [
                (new ElementName(''))->withOptions(['title' => true]),
            ],
            'class-list',
        );

        $context = new DescriptorAwareCompilerContext(
            new ProjectNode(),
            new VersionDescriptor('1.0.0', new Collection()),
        );

        $transformer = new ClassListNodeTransformer($engine->reveal());
        $result = $transformer->leaveNode($node, $context);

        $this->assertEquals(
            [
                $this->createSectionNode($descriptor1),
                $this->createSectionNode($descriptor2),
            ],
            $result->getChildren(),
        );
    }

    private function createDescriptor(string $className): ElementInterface
    {
        $descriptor1 = $this->prophesize(ElementInterface::class);
        $descriptor1->getFullyQualifiedStructuralElementName()->willReturn(new Fqsen($className));

        return $descriptor1->reveal();
    }

    private function createSectionNode(ElementInterface $descriptor): SectionNode
    {
        return new SectionNode(
            new TitleNode(
                new InlineCompoundNode(
                    [
                        (new PHPReferenceNode('class', $descriptor->getFullyQualifiedStructuralElementName()))
                            ->withDescriptor($descriptor),
                    ],
                ),
                2,
                $descriptor->getName(),
            ),
        );
    }
}
