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
 * @coversDefaultClass \phpDocumentor\Guides\Nodes\SectionBeginNode
 * @covers ::<private>
 */
final class SectionBeginNodeTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTitleNode
     * @covers ::getValue
     */
    public function testASectionOpeningCanBeDefinedWithATitle(): void
    {
        $titleNode = new TitleNode(new RawNode('Title'), 1);

        $node = new SectionBeginNode($titleNode);

        self::assertSame($titleNode, $node->getTitleNode());
        self::assertNull($node->getValue());
    }
}
