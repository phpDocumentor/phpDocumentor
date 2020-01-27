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

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Tests the functionality for the StripInternal class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\StripInternal
 */
final class StripInternalTest extends MockeryTestCase
{
    /** @var ProjectDescriptorBuilder|m\Mock */
    private $builderMock;

    /** @var StripInternal $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->builderMock = m::mock(ProjectDescriptorBuilder::class);
        $this->fixture = new StripInternal($this->builderMock);
    }

    /**
     * @covers ::__invoke
     */
    public function testStripsInternalTagFromDescription() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);
        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(null);

        $descriptor->shouldReceive('getDescription')->andReturn('without {@internal blabla }}internal tag');
        $descriptor->shouldReceive('setDescription')->with('without internal tag');

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testStripsInternalTagFromDescriptionIfTagDescriptionContainsBraces() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);
        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(null);

        $descriptor->shouldReceive('getDescription')->andReturn('without {@internal bla{bla} }}internal tag');
        $descriptor->shouldReceive('setDescription')->with('without internal tag');

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testResolvesInternalTagFromDescriptionIfParsePrivateIsTrue() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);
        $descriptor = m::mock(DescriptorAbstract::class);

        $descriptor->shouldReceive('getDescription')->andReturn('without {@internal blabla }}internal tag');
        $descriptor->shouldReceive('setDescription')->with('without blabla internal tag');

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testRemovesDescriptorIfTaggedAsInternal() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);

        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getDescription');
        $descriptor->shouldReceive('setDescription');
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(true);

        $this->assertNull($this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testKeepsDescriptorIfTaggedAsInternalAndParsePrivateIsTrue() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);

        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getDescription');
        $descriptor->shouldReceive('setDescription');
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(true);

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers ::__invoke
     */
    public function testDescriptorIsUnmodifiedIfThereIsNoInternalTag() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);

        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getDescription');
        $descriptor->shouldReceive('setDescription');
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(false);

        $this->assertEquals($descriptor, $this->fixture->__invoke($descriptor));
    }
}
