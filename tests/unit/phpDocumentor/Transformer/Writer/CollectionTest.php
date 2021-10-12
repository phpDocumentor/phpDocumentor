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
use stdClass;

/**
 * Test class for phpDocumentor\Transformer\Writer\Collection
 */
final class CollectionTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @covers \phpDocumentor\Transformer\Writer\Collection::offsetSet
     */
    public function testThrowsErrorOnInvalidWriterRegistration(): void
    {
        $this->expectException('InvalidArgumentException');
        new Collection(['key' => new stdClass()]);
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Collection::get
     */
    public function testOffsetGetWithNonExistingIndex(): void
    {
        $this->expectException('InvalidArgumentException');
        (new Collection([]))->get('nonExistingIndex');
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Collection::get
     */
    public function testOffsetGetWithExistingIndex(): void
    {
        $writer = $this->prophesize(WriterAbstract::class);
        $fixture = new Collection(['key' => $writer->reveal()]);

        self::assertSame($writer->reveal(), $fixture->get('key'));
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Collection::checkRequirements
     */
    public function testCheckRequirements(): void
    {
        $writer = $this->prophesize(WriterAbstract::class);
        $fixture = new Collection(['key' => $writer->reveal()]);

        $writer->checkRequirements()->shouldBeCalledOnce();
        $fixture->checkRequirements();
    }
}
