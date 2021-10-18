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

final class DefinitionListNodeTest extends TestCase
{
    public function test_it_can_be_created_with_a_definition_list(): void
    {
        $definitionList = new DefinitionList([]);

        $node = new DefinitionListNode($definitionList);

        self::assertSame($definitionList, $node->getDefinitionList());
    }
}
