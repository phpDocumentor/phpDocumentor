<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\NodeFactory;

use phpDocumentor\Guides\Nodes\DefinitionListNode;
use phpDocumentor\Guides\Nodes\Factory;
use phpDocumentor\Guides\Nodes\SpanNode;
use phpDocumentor\Guides\Nodes\TableNode;
use phpDocumentor\Guides\RestructuredText\Parser;
use phpDocumentor\Guides\RestructuredText\Parser\DefinitionList;
use phpDocumentor\Guides\RestructuredText\Parser\LineChecker;
use phpDocumentor\Guides\RestructuredText\Parser\TableSeparatorLineConfig;

interface NodeFactory extends Factory
{
    public function createTableNode(
        TableSeparatorLineConfig $separatorLineConfig,
        string $type,
        LineChecker $lineChecker
    ) : TableNode;

    /**
     * @param string|string[]|SpanNode $span
     */
    public function createSpanNode(Parser $parser, $span) : SpanNode;

    public function createDefinitionListNode(DefinitionList $definitionList) : DefinitionListNode;
}
