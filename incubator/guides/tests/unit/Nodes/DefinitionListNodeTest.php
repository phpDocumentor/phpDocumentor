<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes;

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

use phpDocumentor\Guides\Nodes\DefinitionLists\DefinitionList;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Guides\Nodes\DefinitionListNode
 * @covers ::<private>
 */
final class DefinitionListNodeTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getDefinitionList
     */
    public function testItCanBeCreatedWithADefinitionList(): void
    {
        $definitionList = new DefinitionList([]);

        $node = new DefinitionListNode($definitionList);

        self::assertSame($definitionList, $node->getDefinitionList());
    }
}
