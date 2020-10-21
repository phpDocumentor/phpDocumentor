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

final class AnchorNodeTest extends TestCase
{
    public function test_it_can_be_created_with_a_value() : void
    {
        $node = new AnchorNode('value');

        $this->assertSame('value', $node->getValue());
        $this->assertSame('value', $node->getValueString());
    }

    public function test_you_can_pass_classes_for_in_templates() : void
    {
        $classes = ['anchor', 'node'];

        $node = new AnchorNode('value');
        $node->setClasses($classes);

        $this->assertSame($classes, $node->getClasses());
        $this->assertSame('anchor node', $node->getClassesString());
    }
}
