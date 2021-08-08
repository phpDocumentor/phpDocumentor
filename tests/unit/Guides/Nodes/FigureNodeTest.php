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

final class FigureNodeTest extends TestCase
{
    public function test_it_can_be_created_with_an_image_and_caption(): void
    {
        $image = new ImageNode();
        $document = new RawNode('raw');

        $node = new FigureNode($image, $document);

        self::assertSame($image, $node->getImage());
        self::assertSame($document, $node->getDocument());
    }
}
