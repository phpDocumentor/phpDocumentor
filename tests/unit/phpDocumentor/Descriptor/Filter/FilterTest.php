<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use League\Pipeline\Pipeline;
use \Mockery as m;

/**
 * Tests the functionality for the Filter class.
 */
class FilterTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    const FQCN = 'SomeFilterClass';

    /** @var ClassFactory|m\Mock */
    protected $classFactoryMock;

    /** @var FilterInterface|m\Mock */
    protected $filterChainMock;

    /** @var Filter $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->classFactoryMock = m::mock('phpDocumentor\Descriptor\Filter\ClassFactory');
        $this->filterChainMock = m::mock(Pipeline::class);
        $this->fixture = new Filter($this->classFactoryMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\Filter::__construct
     */
    public function testClassFactoryIsSetUponConstruction()
    {
        $this->assertAttributeSame($this->classFactoryMock, 'factory', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\Filter::attach
     */
    public function testAttach()
    {
        $filterMock = m::mock(FilterInterface::class);

        $this->classFactoryMock->shouldReceive('attachTo')->with(self::FQCN, $filterMock);

        $this->fixture->attach(self::FQCN, $filterMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\Filter::filter
     */
    public function testFilter()
    {
        $filterableMock = m::mock('phpDocumentor\Descriptor\Filter\Filterable');

        $this->filterChainMock->shouldReceive('__invoke')->with($filterableMock)->andReturn($filterableMock);
        $this->classFactoryMock
            ->shouldReceive('getChainFor')->with(get_class($filterableMock))->andReturn($this->filterChainMock);

        $this->assertSame($filterableMock, $this->fixture->filter($filterableMock));
    }
}
