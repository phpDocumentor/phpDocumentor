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

namespace phpDocumentor\Guides\Nodes\DefinitionLists;

use phpDocumentor\Guides\Nodes\SpanNode;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Guides\Nodes\DefinitionLists\DefinitionList
 * @covers ::<private>
 */
final class DefinitionListTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTerms
     */
    public function testProvideASetOfTerms(): void
    {
        $terms = [new DefinitionListTerm($this->prophesize(SpanNode::class)->reveal(), [], [])];

        $definitionList = new DefinitionList($terms);

        self::assertSame($terms, $definitionList->getTerms());
    }
}
