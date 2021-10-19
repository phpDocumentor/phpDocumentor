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
 * @coversDefaultClass \phpDocumentor\Guides\Nodes\TemplatedNode
 * @covers ::<private>
 */
final class TemplatedNodeTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getValue
     * @covers ::getData
     */
    public function testTemplateNameAndAttributesCanBeInvokedWithThisNode(): void
    {
        $node = new TemplatedNode('template.html.twig', ['myData' => 1]);

        self::assertSame('template.html.twig', $node->getValue());
        self::assertSame(['myData' => 1], $node->getData());
    }
}
