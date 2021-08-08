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

final class ListNodeTest extends TestCase
{
    public function test_it_can_be_return_information_on_each_line_in_a_list(): void
    {
        $list = [
            [
                'text' => 'the line text for line 1',
                'depth' => 0,
                'prefix' => '*',
                'ordered' => false,
            ],
        ];

        $node = new ListNode();
        $node->addLine($list[0]);

        self::assertSame($list, $node->getLines());
    }
}
