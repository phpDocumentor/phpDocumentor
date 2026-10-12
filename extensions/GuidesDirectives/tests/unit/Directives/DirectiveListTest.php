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
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Interfaces\VersionInterface;
use phpDocumentor\Descriptor\ValueObjects\CallArgument;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Nodes\ProjectNode;
use phpDocumentor\Guides\RestructuredText\Nodes\DirectiveNode;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;
use phpDocumentor\GuidesDirectives\Directives\DirectiveList;
use phpDocumentor\GuidesDirectives\Nodes\DirectiveInfoNode;
use phpDocumentor\GuidesDirectives\Nodes\DirectiveListNode;
use phpDocumentor\GuidesDirectives\Nodes\DirectiveOptionNode;
use phpDocumentor\Query\Engine;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function array_map;
use function implode;

#[CoversClass(DirectiveList::class)]
#[CoversClass(DirectiveListNode::class)]
#[CoversClass(DirectiveInfoNode::class)]
#[CoversClass(DirectiveOptionNode::class)]
final class DirectiveListTest extends TestCase
{
    private const ATTRIBUTE_NAMESPACE = '\\phpDocumentor\\Guides\\RestructuredText\\Directives\\Attributes\\';

    // Scenario: Every non-abstract directive class is listed in alphabetical order with an anchor

    #[Test]
    public function itListsDirectivesSortedByNameWithAnAnchor(): void
    {
        $node = $this->createListNode(
            $this->directiveClass('zeta'),
            $this->directiveClass('alpha'),
        );

        self::assertInstanceOf(DirectiveListNode::class, $node);
        $infos = $this->infoNodes($node);
        self::assertSame(['alpha', 'zeta'], array_map(static fn (DirectiveInfoNode $i) => $i->getName(), $infos));
        self::assertSame('directive-alpha', $infos[0]->getAnchor());
        self::assertSame('directive-zeta', $infos[1]->getAnchor());
    }

    // Scenario: Aliases are shown

    #[Test]
    public function itExposesTheAliasesOfADirective(): void
    {
        $node = $this->createListNode($this->directiveClass('example-directive', aliases: ['example', 'sample']));

        self::assertSame(['example', 'sample'], $this->infoNodes($node)[0]->getAliases());
    }

    // Scenario: The aliases line is omitted without aliases

    #[Test]
    public function itHasNoAliasesWhenTheAttributeDefinesNone(): void
    {
        $node = $this->createListNode($this->directiveClass('example-directive'));

        self::assertSame([], $this->infoNodes($node)[0]->getAliases());
    }

    // Scenario: The class docblock is shown as the description

    #[Test]
    public function itUsesSummaryAndDescriptionOfTheClassAsDescription(): void
    {
        $class = $this->directiveClass('example-directive');
        $class->setSummary('The summary.');
        $class->setDescription(new DescriptionDescriptor(new Description('The description.'), []));

        $node = $this->createListNode($class);

        self::assertSame("The summary.\n\nThe description.", $this->infoNodes($node)[0]->getDescription());
    }

    // Scenario: Docblock tags are not shown

    #[Test]
    public function itDoesNotIncludeDocblockTagsInTheDescription(): void
    {
        $class = $this->directiveClass('example-directive');
        $class->setSummary('The summary.');

        $node = $this->createListNode($class);

        self::assertStringNotContainsString('@deprecated', $this->infoNodes($node)[0]->getDescription());
    }

    // Scenario: Options are listed with their details

    #[Test]
    public function itAddsAnOptionNodeForEachOptionAttribute(): void
    {
        $class = $this->directiveClass('example-directive');
        $class->addAttribute($this->optionAttribute([
            'name' => "'template'",
            'type' => 'OptionType::String',
            'description' => "'The template.'",
        ]));
        $class->addAttribute($this->optionAttribute([
            'name' => "'force'",
            'type' => 'OptionType::Boolean',
            'default' => 'false',
            'description' => "'Overwrite.'",
        ]));

        $options = $this->infoNodes($this->createListNode($class))[0]->getChildren();

        self::assertCount(2, $options);
        self::assertContainsOnlyInstancesOf(DirectiveOptionNode::class, $options);
        self::assertSame('template', $options[0]->getName());
        self::assertSame('string', $options[0]->getType());
        self::assertSame('The template.', $options[0]->getDescription());
        self::assertSame('force', $options[1]->getName());
        self::assertSame('boolean', $options[1]->getType());
        self::assertSame('false', $options[1]->getDefault());
    }

    // Scenario: An option without a type is shown as a string

    #[Test]
    public function itDefaultsTheTypeOfAnOptionToString(): void
    {
        $class = $this->directiveClass('example-directive');
        $class->addAttribute($this->optionAttribute(['name' => "'template'"]));

        $options = $this->infoNodes($this->createListNode($class))[0]->getChildren();

        self::assertSame('string', $options[0]->getType());
    }

    // Scenario: The options section is omitted without options

    #[Test]
    public function itHasNoOptionNodesWithoutOptionAttributes(): void
    {
        $node = $this->createListNode($this->directiveClass('example-directive'));

        self::assertSame([], $this->infoNodes($node)[0]->getChildren());
    }

    #[Test]
    public function itIgnoresAttributesNamedOptionFromOtherNamespaces(): void
    {
        $class = $this->directiveClass('example-directive');
        $class->addAttribute($this->optionAttribute(['name' => "'foreign'"], '\\Other\\Option'));

        self::assertSame([], $this->infoNodes($this->createListNode($class))[0]->getChildren());
    }

    #[Test]
    public function itListsADirectiveNameOnlyOnce(): void
    {
        $node = $this->createListNode(
            $this->directiveClass('example-directive'),
            $this->directiveClass('example-directive'),
        );

        self::assertCount(1, $this->infoNodes($node));
    }

    // Scenario: Abstract directive classes are not listed

    #[Test]
    public function itSkipsAbstractClasses(): void
    {
        $abstract = $this->directiveClass('abstract-directive');
        $abstract->setAbstract(true);

        $node = $this->createListNode($this->directiveClass('example-directive'), $abstract);

        self::assertSame(
            ['example-directive'],
            array_map(static fn (DirectiveInfoNode $i) => $i->getName(), $this->infoNodes($node)),
        );
    }

    // Scenario: Nothing is rendered without directives

    #[Test]
    public function itReturnsNullWhenNoDirectivesAreFound(): void
    {
        self::assertNull($this->createListNode());
    }

    #[Test]
    public function itReturnsNullWithoutADescriptorAwareCompilerContext(): void
    {
        $engine = $this->createMock(Engine::class);
        $engine->expects(self::never())->method('perform');

        $directive = new DirectiveList($engine);

        self::assertNull($directive->createNode($this->directiveNode(), null));
    }

    private function createListNode(ClassDescriptor ...$classes): DirectiveListNode|null
    {
        $engine = $this->createMock(Engine::class);
        $engine->method('perform')->willReturn(new ArrayIterator($classes));

        $node = (new DirectiveList($engine))->createNode($this->directiveNode(), $this->compilerContext());
        self::assertTrue($node === null || $node instanceof DirectiveListNode);

        return $node;
    }

    /** @return list<DirectiveInfoNode> */
    private function infoNodes(DirectiveListNode|null $node): array
    {
        self::assertNotNull($node);
        $children = $node->getChildren();
        self::assertContainsOnlyInstancesOf(DirectiveInfoNode::class, $children);

        return $children;
    }

    /** @param list<string> $aliases */
    private function directiveClass(string $name, array $aliases = []): ClassDescriptor
    {
        $attribute = new AttributeDescriptor();
        $attribute->setName('Directive');
        $attribute->setFullyQualifiedStructuralElementName(new Fqsen(self::ATTRIBUTE_NAMESPACE . 'Directive'));
        $attribute->addArgument(new CallArgument("'" . $name . "'", 'name'));
        if ($aliases !== []) {
            $quoted = array_map(static fn (string $alias) => "'" . $alias . "'", $aliases);
            $attribute->addArgument(new CallArgument('[' . implode(', ', $quoted) . ']', 'aliases'));
        }

        $class = new ClassDescriptor();
        $class->addAttribute($attribute);

        return $class;
    }

    /** @param array<string, string> $arguments */
    private function optionAttribute(
        array $arguments,
        string $fqsen = self::ATTRIBUTE_NAMESPACE . 'Option',
    ): AttributeDescriptor {
        $attribute = new AttributeDescriptor();
        $attribute->setName('Option');
        $attribute->setFullyQualifiedStructuralElementName(new Fqsen($fqsen));
        foreach ($arguments as $name => $value) {
            $attribute->addArgument(new CallArgument($value, $name));
        }

        return $attribute;
    }

    private function directiveNode(): DirectiveNode
    {
        return new DirectiveNode(new Directive('', 'phpdoc-guides:directive-list', ''));
    }

    private function compilerContext(): DescriptorAwareCompilerContext
    {
        return new DescriptorAwareCompilerContext(
            new ProjectNode(),
            $this->createMock(VersionInterface::class),
        );
    }
}
