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

use \Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Tests the functionality for the StripInternal class.
 */
class StripInternalTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ProjectDescriptorBuilder|m\Mock */
    protected $builderMock;

    /** @var StripInternal $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new StripInternal($this->builderMock);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripInternal::__invoke
     */
    public function testStripsInternalTagFromDescription() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);
        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(null);

        $descriptor->shouldReceive('getDescription')->andReturn('without {@internal blabla }}internal tag');
        $descriptor->shouldReceive('setDescription')->with('without internal tag');

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripInternal::__invoke
     */
    public function testStripsInternalTagFromDescriptionIfTagDescriptionContainsBraces() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);
        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(null);

        $descriptor->shouldReceive('getDescription')->andReturn('without {@internal bla{bla} }}internal tag');
        $descriptor->shouldReceive('setDescription')->with('without internal tag');

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripInternal::__invoke
     */
    public function testResolvesInternalTagFromDescriptionIfParsePrivateIsTrue() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);
        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');

        $descriptor->shouldReceive('getDescription')->andReturn('without {@internal blabla }}internal tag');
        $descriptor->shouldReceive('setDescription')->with('without blabla internal tag');

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripInternal::__invoke
     */
    public function testRemovesDescriptorIfTaggedAsInternal() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(false);

        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getDescription');
        $descriptor->shouldReceive('setDescription');
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(true);

        $this->assertNull($this->fixture->__invoke($descriptor));
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripInternal::__invoke
     */
    public function testKeepsDescriptorIfTaggedAsInternalAndParsePrivateIsTrue() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);

        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getDescription');
        $descriptor->shouldReceive('setDescription');
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(true);

        $this->assertSame($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripInternal::__invoke
     */
    public function testDescriptorIsUnmodifiedIfThereIsNoInternalTag() : void
    {
        $this->builderMock->shouldReceive('getProjectDescriptor->isVisibilityAllowed')->andReturn(true);

        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getDescription');
        $descriptor->shouldReceive('setDescription');
        $descriptor->shouldReceive('getTags->get')->with('internal')->andReturn(false);

        $this->assertEquals($descriptor, $this->fixture->__invoke($descriptor));
    }
}
