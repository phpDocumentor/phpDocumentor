<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\GuidesDirectives\Tests\Unit\Directives;

use ArrayIterator;
use phpDocumentor\Descriptor\AttributeDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Interfaces\VersionInterface;
use phpDocumentor\Descriptor\ValueObjects\CallArgument;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Nodes\ProjectNode;
use phpDocumentor\Guides\RestructuredText\Nodes\DirectiveNode;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;
use phpDocumentor\GuidesDirectives\Directives\DirectiveOptionNodeFactory;
use phpDocumentor\GuidesDirectives\Directives\DirectiveOptionsList;
use phpDocumentor\GuidesDirectives\Nodes\DirectiveOptionNode;
use phpDocumentor\GuidesDirectives\Nodes\DirectiveOptionsListNode;
use phpDocumentor\Query\Engine;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_map;

#[CoversClass(DirectiveOptionsList::class)]
final class DirectiveOptionsListTest extends TestCase
{
    #[Test]
    public function itReturnsNullWithoutADescriptorAwareCompilerContext(): void
    {
        $engine = $this->createMock(Engine::class);
        $engine->expects(self::never())->method('perform');

        $directive = new DirectiveOptionsList($engine, new DirectiveOptionNodeFactory());

        self::assertNull($directive->createNode($this->directiveNode('my-directive'), null));
    }

    #[Test]
    public function itReturnsNullWhenNoDirectiveMatchesTheGivenName(): void
    {
        $engine = $this->createMock(Engine::class);
        $engine->method('perform')->willReturn(new ArrayIterator([]));

        $directive = new DirectiveOptionsList($engine, new DirectiveOptionNodeFactory());

        $result = $directive->createNode(
            $this->directiveNode('my-directive'),
            $this->compilerContext(),
        );

        self::assertNull($result);
    }

    #[Test]
    public function itReturnsAnOptionsNodeWithAnOptionNodeForEachOption(): void
    {
        $classDescriptor = new ClassDescriptor();
        $classDescriptor->addAttribute($this->optionAttribute('name', "'template'"));
        $classDescriptor->addAttribute($this->optionAttribute('name', "'force'"));

        $engine = $this->createMock(Engine::class);
        $engine->method('perform')->willReturn(new ArrayIterator([$classDescriptor]));

        $directive = new DirectiveOptionsList($engine, new DirectiveOptionNodeFactory());

        $result = $directive->createNode(
            $this->directiveNode('my-directive'),
            $this->compilerContext(),
        );

        self::assertInstanceOf(DirectiveOptionsListNode::class, $result);
        self::assertContainsOnlyInstancesOf(DirectiveOptionNode::class, $result->getChildren());
        self::assertSame(
            ['template', 'force'],
            array_map(static fn (DirectiveOptionNode $option) => $option->getName(), $result->getChildren()),
        );
    }

    private function directiveNode(string $directiveName): DirectiveNode
    {
        return new DirectiveNode(new Directive('', $directiveName, $directiveName));
    }

    private function compilerContext(): DescriptorAwareCompilerContext
    {
        return new DescriptorAwareCompilerContext(
            new ProjectNode(),
            $this->createMock(VersionInterface::class),
        );
    }

    private function optionAttribute(string $argumentName, string $value): AttributeDescriptor
    {
        $attribute = new AttributeDescriptor();
        $attribute->setName('Option');
        $attribute->setFullyQualifiedStructuralElementName(new Fqsen(
            '\\phpDocumentor\\Guides\\RestructuredText\\Directives\\Attributes\\Option',
        ));
        $attribute->addArgument(new CallArgument($value, $argumentName));

        return $attribute;
    }
}
