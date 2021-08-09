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

final class ImageNodeTest extends TestCase
{
    public function test_it_can_be_created_with_a_url(): void
    {
        $url = 'https://example.com/images/image1.jpg';

        $node = new ImageNode($url);

        self::assertSame($url, $node->getValue());
    }

    public function test_it_can_have_a_width_and_height(): void
    {
        $width = '10';
        $height = '20';

        $node = new ImageNode();
        $nodeWithOptions = $node->withOptions(['width' => $width, 'height' => $height]);

        // also test immutability to be sure
        self::assertNull($node->getOption('width'));
        self::assertNull($node->getOption('height'));
        self::assertSame($width, $nodeWithOptions->getOption('width'));
        self::assertSame($height, $nodeWithOptions->getOption('height'));
    }

    public function test_it_can_have_an_alt_text(): void
    {
        $alt = 'alt text';

        $node = new ImageNode();
        $nodeWithOptions = $node->withOptions(['alt' => $alt]);

        // also test immutability to be sure
        self::assertNull($node->getOption('alt'));
        self::assertSame($alt, $nodeWithOptions->getOption('alt'));
    }

    public function test_it_can_have_an_alignment(): void
    {
        $align = 'left';

        $node = new ImageNode();
        $nodeWithOptions = $node->withOptions(['align' => $align]);

        // also test immutability to be sure
        self::assertNull($node->getOption('align'));
        self::assertSame($align, $nodeWithOptions->getOption('align'));
    }

    public function test_you_can_pass_classes_for_in_templates(): void
    {
        $classes = ['image', 'node'];

        $node = new ImageNode();
        $node->setClasses($classes);

        self::assertSame($classes, $node->getClasses());
        self::assertSame('image node', $node->getClassesString());
    }
}
