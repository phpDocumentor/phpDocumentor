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

final class ParagraphNodeTest extends TestCase
{
    public function test_it_can_be_created_with_a_value_even_another_node(): void
    {
        $imageNode = new ImageNode();

        $node = new ParagraphNode($imageNode);

        self::assertSame($imageNode, $node->getValue());
    }
}
