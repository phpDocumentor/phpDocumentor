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
 * @coversDefaultClass \phpDocumentor\Guides\Nodes\SectionEndNode
 * @covers ::<private>
 */
final class SectionEndNodeTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTitleNode
     * @covers ::getValue
     */
    public function testASectionEndCanBeDefinedWithATitle(): void
    {
        $titleNode = new TitleNode(new RawNode('Title'), 1);

        $node = new SectionEndNode($titleNode);

        self::assertSame($titleNode, $node->getTitleNode());
        self::assertNull($node->getValue());
    }
}
