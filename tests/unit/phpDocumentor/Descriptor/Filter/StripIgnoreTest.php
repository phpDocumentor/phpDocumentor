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

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Tests the functionality for the StripIgnore class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\StripIgnore
 */
final class StripIgnoreTest extends TestCase
{
    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    private $builderMock;

    /** @var StripIgnore $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->builderMock = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->fixture = new StripIgnore($this->builderMock->reveal());
    }

    /**
     * @covers ::__invoke
     */
    public function testStripsIgnoreTagFromDescription() : void
    {
        $collection = $this->prophesize(Collection::class);
        $collection->fetch('ignore')->shouldBeCalled()->willReturn(true);

        $descriptor = $this->prophesize(DescriptorAbstract::class);
        $descriptor->getTags()->shouldBeCalled()->willReturn($collection->reveal());

        $this->assertNull($this->fixture->__invoke($descriptor->reveal()));
    }

    /**
     * @covers ::__invoke
     */
    public function testDescriptorIsUnmodifiedIfThereIsNoIgnoreTag() : void
    {
        $collection = $this->prophesize(Collection::class);
        $collection->fetch('ignore')->shouldBeCalled()->willReturn(false);

        $descriptor = $this->prophesize(DescriptorAbstract::class);
        $descriptor->getTags()->shouldBeCalled()->willReturn($collection->reveal());

        $this->assertEquals($descriptor->reveal(), $this->fixture->__invoke($descriptor->reveal()));
    }
}
