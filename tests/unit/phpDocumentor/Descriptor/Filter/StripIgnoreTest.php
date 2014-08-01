<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use \Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Tests the functionality for the StripIgnore class.
 */
class StripIgnoreTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectDescriptorBuilder|m\Mock */
    protected $builderMock;

    /** @var StripIgnore $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new StripIgnore($this->builderMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\StripIgnore::__construct
     */
    public function testProjectDescriptorBuilderIsSetUponConstruction()
    {
        $this->assertAttributeSame($this->builderMock, 'builder', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\StripIgnore::filter
     */
    public function testStripsIgnoreTagFromDescription()
    {
        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getTags->get')->with('ignore')->andReturn(true);

        $this->assertSame(null, $this->fixture->filter($descriptor));
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\StripIgnore::filter
     */
    public function testDescriptorIsUnmodifiedIfThereIsNoIgnoreTag()
    {
        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getTags->get')->with('ignore')->andReturn(false);

        // we clone the descriptor so its references differ; if something changes in the descriptor then
        // the $descriptor variable and the returned clone will differ
        $this->assertEquals($descriptor, $this->fixture->filter(clone $descriptor));
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\StripIgnore::filter
     */
    public function testNullIsReturnedIfThereIsNoDescriptor()
    {
        $this->assertSame(null, $this->fixture->filter(null));
    }
}
