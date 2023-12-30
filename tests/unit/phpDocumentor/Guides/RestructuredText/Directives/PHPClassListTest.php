<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\CollectionNode;
use phpDocumentor\Guides\Nodes\InlineCompoundNode;
use phpDocumentor\Guides\Nodes\PHP\ClassList;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;
use phpDocumentor\Guides\RestructuredText\Parser\Productions\Rule;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class PHPClassListTest extends TestCase
{
    use ProphecyTrait;

    public function testGetName(): void
    {
        $directive = new PHPClassList($this->prophesize(Rule::class)->reveal());

        $this->assertSame('phpdoc:class-list', $directive->getName());
    }

    public function testProcessReturnsPHPClassListNode(): void
    {
        $rule = $this->prophesize(Rule::class);
        $rule->apply(Argument::any())->willReturn(new CollectionNode([new InlineCompoundNode([])]));
        $directive = new PHPClassList($rule->reveal());

        $node = $directive->process(
            $this->prophesize(BlockContext::class)->reveal(),
            new Directive('variable', 'phpdoc:class-list', '[*]'),
        );

        $this->assertInstanceOf(ClassList::class, $node);
        $this->assertCount(1, $node->getBlueprint());
        $this->assertSame(
            "$.documentationSets.*[?(type(@) == 'ApiSetDescriptor')].indexes.classes.*[*]",
            $node->getQuery(),
        );
    }
}
