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

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Guides\Nodes\ListItemNode
 * @covers ::<private>
 */
final class ListItemNodeTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getPrefix
     * @covers ::isOrdered
     * @covers ::getContents
     * @covers ::getValue
     */
    public function testPrefixingCharacterTypeOfListAndContentsOfItemCanBeRecorded(): void
    {
        $contents = [
            new RawNode('contents1'),
            new RawNode('contents2'),
        ];
        $node = new ListItemNode('*', true, $contents);

        self::assertSame('*', $node->getPrefix());
        self::assertTrue($node->isOrdered());
        self::assertSame($contents, $node->getContents());
        self::assertNull($node->getValue());
    }

    /**
     * @covers ::getContentsAsString
     */
    public function testContentsCanBeMappedToString(): void
    {
        $contents = [
            new RawNode('contents1'),
            new RawNode('contents2'),
        ];
        $node = new ListItemNode('*', true, $contents);

        self::assertSame("contents1\ncontents2", $node->getContentsAsString());
    }
}
