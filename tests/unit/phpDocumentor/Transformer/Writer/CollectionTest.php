<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use phpDocumentor\Transformer\Router\Queue;

/**
 * Test class for phpDocumentor\Transformer\Writer\Collection
 */
class CollectionTest extends MockeryTestCase
{
    /** @var MockInterface|Queue */
    protected $routers;

    /** @var MockInterface|WriterAbstract */
    protected $writer;

    /** @var Collection */
    protected $fixture;

    /**
     * Initializes the fixture and dependencies for this testcase.
     */
    protected function setUp()
    {
        $this->routers = m::mock(Queue::class);
        $this->writer = m::mock(WriterAbstract::class);
        $this->fixture = new Collection($this->routers);
    }

    /**
     * @covers phpDocumentor\Transformer\Writer\Collection::__construct
     */
    public function testIfDependenciesAreCorrectlyRegisteredOnInitialization()
    {
        $this->assertAttributeSame($this->routers, 'routers', $this->fixture);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @covers phpDocumentor\Transformer\Writer\Collection::offsetSet
     */
    public function testOffsetSetWithWriterNotDescendingFromWriterAbstract()
    {
        $this->fixture->offsetSet('index', new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @covers phpDocumentor\Transformer\Writer\Collection::offsetSet
     */
    public function testOffsetSetWithInvalidIndexName()
    {
        $this->fixture->offsetSet('i', $this->writer);
    }

    /**
     * @covers phpDocumentor\Transformer\Writer\Collection::offsetSet
     */
    public function testOffsetSetWriterReceivesRouterQueue()
    {
        $this->writer->shouldReceive('setRouters')->once()->with($this->routers);
        $this->fixture->offsetSet('index', $this->writer);

        $this->assertTrue(true);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @covers phpDocumentor\Transformer\Writer\Collection::offsetGet
     */
    public function testOffsetGetWithNonExistingIndex()
    {
        $this->fixture->offsetGet('nonExistingIndex');
    }

    /**
     * @covers phpDocumentor\Transformer\Writer\Collection::offsetGet
     */
    public function testOffsetGetWithExistingIndex()
    {
        $this->registerWriter();

        $this->assertSame($this->writer, $this->fixture->offsetGet('index'));
    }

    /**
     * @covers phpDocumentor\Transformer\Writer\Collection::checkRequirements
     */
    public function testCheckRequirements()
    {
        $this->registerWriter();

        $this->writer->shouldReceive('checkRequirements')->once();
        $this->fixture->checkRequirements();

        $this->assertTrue(true);
    }

    /**
     * Registers a writer for tests that need a collection item
     */
    private function registerWriter()
    {
        $this->writer->shouldReceive('setRouters')->with($this->routers);
        $this->fixture->offsetSet('index', $this->writer);
    }
}
