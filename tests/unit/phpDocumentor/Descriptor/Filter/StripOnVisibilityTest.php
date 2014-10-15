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
 * Tests the functionality for the StripOnVisibility class.
 */
class StripOnVisibilityTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectDescriptorBuilder|m\Mock */
    protected $builderMock;

    /** @var StripOnVisibility $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new StripOnVisibility($this->builderMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\StripOnVisibility::__construct
     */
    public function testProjectDescriptorBuilderIsSetUponConstruction()
    {
        $this->assertAttributeSame($this->builderMock, 'builder', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\StripOnVisibility::filter
     */
    public function testStripsTagFromDescriptionIfVisibilityIsNotAllowed()
    {
        $this->builderMock->shouldReceive('isVisibilityAllowed')->andReturn(false);

        $descriptor = m::mock('phpDocumentor\Descriptor\Interfaces\VisibilityInterface');
        $descriptor->shouldReceive('getVisibility');

        $this->assertSame(null, $this->fixture->filter($descriptor));
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\StripOnVisibility::filter
     */
    public function testKeepsDescriptorIfVisibilityIsAllowed()
    {
        $this->builderMock->shouldReceive('isVisibilityAllowed')->andReturn(true);

        $descriptor = m::mock('phpDocumentor\Descriptor\Interfaces\VisibilityInterface');
        $descriptor->shouldReceive('getVisibility');

        $this->assertSame($descriptor, $this->fixture->filter($descriptor));
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\StripOnVisibility::filter
     */
    public function testKeepsDescriptorIfDescriptorNotInstanceOfVisibilityInterface()
    {
        $this->builderMock->shouldReceive('isVisibilityAllowed')->andReturn(false);

        $descriptor = m::mock('\phpDocumentor\Descriptor\DescriptorAbstract');

        $this->assertSame($descriptor, $this->fixture->filter($descriptor));
    }
}
