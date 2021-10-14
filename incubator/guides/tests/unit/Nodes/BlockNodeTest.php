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

final class BlockNodeTest extends TestCase
{
    public function test_it_can_be_created_with_series_of_lines(): void
    {
        $node = new BlockNode(['line1', 'line2']);

        self::assertSame("line1\nline2", $node->getValue());
        self::assertSame("line1\nline2", $node->getValueString());
    }

    public function test_lines_are_normalized_by_removing_whitespace(): void
    {
        $node = new BlockNode([
            '  line1',
            '    line2',
            '      line3',
            "\t\t\tline4",
        ]);

        self::assertSame("line1\n  line2\n    line3\n\tline4", $node->getValue());
        self::assertSame("line1\n  line2\n    line3\n\tline4", $node->getValueString());
    }

    public function test_that_normalizing_keeps_spaces_intact_when_the_first_line_has_no_spaces(): void
    {
        $node = new BlockNode([
            'line1',
            '  line2',
            '    line3',
            "\t\t\tline4",
        ]);

        self::assertSame("line1\n  line2\n    line3\n\t\t\tline4", $node->getValue());
        self::assertSame("line1\n  line2\n    line3\n\t\t\tline4", $node->getValueString());
    }
}
