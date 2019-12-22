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

namespace phpDocumentor\Descriptor\Filter;

use League\Pipeline\Pipeline;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use function get_class;

/**
 * Tests the functionality for the Filter class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\Filter
 * @covers ::__construct
 */
final class FilterTest extends MockeryTestCase
{
    public const FQCN = 'SomeFilterClass';

    /** @var ClassFactory|m\Mock */
    protected $classFactoryMock;

    /** @var FilterInterface|m\Mock */
    protected $filterChainMock;

    /** @var Filter $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->classFactoryMock = m::mock(ClassFactory::class);
        $this->filterChainMock = m::mock(Pipeline::class);
        $this->fixture = new Filter($this->classFactoryMock);
    }

    /**
     * @covers ::attach
     */
    public function testAttach() : void
    {
        $filterMock = m::mock(FilterInterface::class);

        $this->classFactoryMock->shouldReceive('attachTo')->with(self::FQCN, $filterMock);

        $this->fixture->attach(self::FQCN, $filterMock);
    }

    /**
     * @covers ::filter
     */
    public function testFilter() : void
    {
        $filterableMock = m::mock(Filterable::class);

        $this->filterChainMock->shouldReceive('__invoke')->with($filterableMock)->andReturn($filterableMock);
        $this->classFactoryMock
            ->shouldReceive('getChainFor')->with(get_class($filterableMock))->andReturn($this->filterChainMock);

        $this->assertSame($filterableMock, $this->fixture->filter($filterableMock));
    }
}
