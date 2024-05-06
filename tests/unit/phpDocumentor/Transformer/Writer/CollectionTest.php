<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/** @coversDefaultClass \phpDocumentor\Transformer\Writer\Collection */
final class CollectionTest extends TestCase
{
    use ProphecyTrait;

    public function testOffsetGetWithNonExistingIndex(): void
    {
        $this->expectException('InvalidArgumentException');

        (new Collection([]))->get('nonExistingIndex');
    }

    public function testOffsetGetWithExistingIndex(): void
    {
        $writer = $this->prophesize(WriterAbstract::class);
        $writer->getName()->willReturn('key');
        $fixture = new Collection([$writer->reveal()]);

        self::assertSame($writer->reveal(), $fixture->get('key'));
    }

    public function testCheckRequirements(): void
    {
        $writer = $this->prophesize(WriterAbstract::class);
        $writer->getName()->willReturn('key');
        $fixture = new Collection([$writer->reveal()]);

        $writer->checkRequirements()->shouldBeCalledOnce();
        $fixture->checkRequirements();
    }
}
