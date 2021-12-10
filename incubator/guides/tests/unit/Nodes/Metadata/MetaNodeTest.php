<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\Metadata;

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

use PHPUnit\Framework\TestCase;

final class MetaNodeTest extends TestCase
{
    public function test_it_can_be_created_with_a_key_and_value(): void
    {
        $node = new MetaNode('key', 'value');

        self::assertSame('key', $node->getKey());
        self::assertSame('value', $node->getValue());
    }
}
