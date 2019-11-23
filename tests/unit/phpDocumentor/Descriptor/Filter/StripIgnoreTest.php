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
 * Tests the functionality for the StripIgnore class.
 */
class StripIgnoreTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ProjectDescriptorBuilder|m\Mock */
    protected $builderMock;

    /** @var StripIgnore $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new StripIgnore($this->builderMock);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripIgnore::__construct
     */
    public function testProjectDescriptorBuilderIsSetUponConstruction() : void
    {
        $this->assertAttributeSame($this->builderMock, 'builder', $this->fixture);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripIgnore::__invoke
     */
    public function testStripsIgnoreTagFromDescription() : void
    {
        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getTags->get')->with('ignore')->andReturn(true);

        $this->assertNull($this->fixture->__invoke($descriptor));
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripIgnore::__invoke
     */
    public function testDescriptorIsUnmodifiedIfThereIsNoIgnoreTag() : void
    {
        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getTags->get')->with('ignore')->andReturn(false);

        $this->assertEquals($descriptor, $this->fixture->__invoke($descriptor));
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\StripIgnore::__invoke
     */
    public function testNullIsReturnedIfThereIsNoDescriptor() : void
    {
        $this->assertNull($this->fixture->__invoke(null));
    }
}
