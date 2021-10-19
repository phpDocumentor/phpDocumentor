<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\DefinitionLists;

use phpDocumentor\Guides\Nodes\SpanNode;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \phpDocumentor\Guides\Nodes\DefinitionLists\DefinitionListTerm
 * @covers ::<private>
 */
final class DefinitionListTermTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTerm
     */
    public function testTheDefinitionTermTextIsAvailable(): void
    {
        $term = $this->prophesize(SpanNode::class)->reveal();

        $definitionListTerm = new DefinitionListTerm($term, [], []);

        self::assertSame($term, $definitionListTerm->getTerm());
    }

    /**
     * @covers ::__construct
     * @covers ::getClassifiers
     */
    public function testClassifiersAreMadeAvailable(): void
    {
        $term = $this->prophesize(SpanNode::class)->reveal();
        $classifier = $this->prophesize(SpanNode::class)->reveal();

        $definitionListTerm = new DefinitionListTerm($term, [$classifier], []);

        self::assertSame([$classifier], $definitionListTerm->getClassifiers());
    }

    /**
     * @covers ::__construct
     * @covers ::getDefinitions
     * @covers ::getFirstDefinition
     */
    public function testDefinitionsAreMadeAvailable(): void
    {
        $term = $this->prophesize(SpanNode::class)->reveal();
        $definition1 = $this->prophesize(SpanNode::class)->reveal();
        $definition2 = $this->prophesize(SpanNode::class)->reveal();

        $definitionListTerm = new DefinitionListTerm($term, [], [$definition1, $definition2]);

        self::assertSame([$definition1, $definition2], $definitionListTerm->getDefinitions());
        self::assertSame($definition1, $definitionListTerm->getFirstDefinition());
    }

    /**
     * @covers ::getFirstDefinition
     */
    public function testGettingFirstDefinitionFailsIfNoDefinitionsAreAvailable(): void
    {
        $this->expectException(RuntimeException::class);

        $term = $this->prophesize(SpanNode::class)->reveal();

        $definitionListTerm = new DefinitionListTerm($term, [], []);

        $definitionListTerm->getFirstDefinition();
    }
}
