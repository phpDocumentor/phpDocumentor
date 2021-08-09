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

final class GenericNodeTest extends TestCase
{
    public function test_it_can_be_created_with_a_name_and_value(): void
    {
        $node = new GenericNode('name', 'value');

        self::assertSame('name', $node->getName());
        self::assertSame('value', $node->getValue());
    }

    public function test_it_can_have_options(): void
    {
        $option = 'option';

        $node = new GenericNode('name', 'value');
        $nodeWithOptions = $node->withOptions(['option' => $option]);

        // also test immutability to be sure
        self::assertNull($node->getOption('option'));
        self::assertSame($option, $nodeWithOptions->getOption('option'));
    }

    public function test_you_can_pass_classes_for_in_templates(): void
    {
        $classes = ['generic', 'node'];

        $node = new GenericNode('name', 'value');
        $node->setClasses($classes);

        self::assertSame($classes, $node->getClasses());
        self::assertSame('generic node', $node->getClassesString());
    }
}
