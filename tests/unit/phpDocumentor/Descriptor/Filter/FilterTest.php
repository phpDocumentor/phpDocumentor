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

namespace phpDocumentor\Descriptor\Filter;

use League\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use function get_class;

/**
 * Tests the functionality for the Filter class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\Filter
 * @covers ::__construct
 */
final class FilterTest extends TestCase
{
    public const FQCN = 'SomeFilterClass';

    /** @var ClassFactory|ObjectProphecy */
    protected $classFactoryMock;

    /** @var FilterInterface|ObjectProphecy */
    protected $filterChainMock;

    /** @var Filter $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->classFactoryMock = $this->prophesize(ClassFactory::class);
        $this->filterChainMock = $this->prophesize(Pipeline::class);
        $this->fixture = new Filter($this->classFactoryMock->reveal());
    }

    /**
     * @covers ::attach
     */
    public function testAttach() : void
    {
        $filterMock = $this->prophesize(FilterInterface::class);

        $this->classFactoryMock->attachTo(self::FQCN, $filterMock->reveal())->shouldBeCalled();

        $this->fixture->attach(self::FQCN, $filterMock->reveal());
    }

    /**
     * @covers ::filter
     */
    public function testFilter() : void
    {
        $filterableMock = $this->prophesize(Filterable::class)->reveal();

        $this->filterChainMock
            ->__invoke($filterableMock)
            ->shouldBeCalled()
            ->willReturn($filterableMock);

        $this->classFactoryMock
            ->getChainFor(get_class($filterableMock))
            ->shouldBeCalled()
            ->willReturn($this->filterChainMock->reveal());

        $this->assertSame($filterableMock, $this->fixture->filter($filterableMock));
    }
}
