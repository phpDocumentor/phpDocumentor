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
 * @coversDefaultClass \phpDocumentor\Guides\Nodes\SeparatorNode
 * @covers ::<private>
 */
final class SeparatorNodeTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getLevel
     * @covers ::getValue
     */
    public function testASeparatorCanBeDefinedWithALevel(): void
    {
        $node = new SeparatorNode(2);

        self::assertSame(2, $node->getLevel());
        self::assertNull($node->getValue());
    }
}
