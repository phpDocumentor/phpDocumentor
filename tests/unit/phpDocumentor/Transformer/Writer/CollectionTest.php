<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use phpDocumentor\Transformer\Router\Router;
use stdClass;

/**
 * Test class for phpDocumentor\Transformer\Writer\Collection
 */
final class CollectionTest extends MockeryTestCase
{
    /** @var MockInterface|Router */
    private $routers;

    /** @var MockInterface|WriterAbstract */
    private $writer;

    /** @var Collection */
    private $fixture;

    /**
     * Initializes the fixture and dependencies for this testcase.
     */
    protected function setUp() : void
    {
        $this->routers = m::mock(Router::class);
        $this->writer = m::mock(WriterAbstract::class);
        $this->fixture = new Collection($this->routers);
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Collection::offsetSet
     */
    public function testOffsetSetWithWriterNotDescendingFromWriterAbstract() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->offsetSet('index', new stdClass());
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Collection::offsetSet
     */
    public function testOffsetSetWithInvalidIndexName() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->offsetSet('i', $this->writer);
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Collection::offsetGet
     */
    public function testOffsetGetWithNonExistingIndex() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->offsetGet('nonExistingIndex');
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Collection::offsetGet
     */
    public function testOffsetGetWithExistingIndex() : void
    {
        $this->registerWriter();

        $this->assertSame($this->writer, $this->fixture->offsetGet('index'));
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Collection::checkRequirements
     */
    public function testCheckRequirements() : void
    {
        $this->registerWriter();

        $this->writer->shouldReceive('checkRequirements')->once();
        $this->fixture->checkRequirements();

        $this->assertTrue(true);
    }

    /**
     * Registers a writer for tests that need a collection item
     */
    private function registerWriter() : void
    {
        $this->fixture->offsetSet('index', $this->writer);
    }
}
