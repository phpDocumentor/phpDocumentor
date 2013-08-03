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

namespace phpDocumentor\Descriptor;

/**
 * Tests the functionality for the PropertyDescriptor class.
 */
class PropertyDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var PropertyDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new PropertyDescriptor();
    }

    /**
     * @covers phpDocumentor\Descriptor\PropertyDescriptor::isStatic
     * @covers phpDocumentor\Descriptor\PropertyDescriptor::setStatic
     */
    public function testSettingAndGettingWhetherPropertyIsStatic()
    {
        $this->assertFalse($this->fixture->isStatic());

        $this->fixture->setStatic(true);

        $this->assertTrue($this->fixture->isStatic());
    }

    /**
     * @covers phpDocumentor\Descriptor\PropertyDescriptor::getVisibility
     * @covers phpDocumentor\Descriptor\PropertyDescriptor::setVisibility
     */
    public function testSettingAndGettingVisibility()
    {
        $this->assertEquals('public', $this->fixture->getVisibility());

        $this->fixture->setVisibility('private');

        $this->assertEquals('private', $this->fixture->getVisibility());
    }

    /**
     * @covers phpDocumentor\Descriptor\PropertyDescriptor::getTypes
     * @covers phpDocumentor\Descriptor\PropertyDescriptor::setTypes
     */
    public function testSetAndGetTypes()
    {
        $this->assertSame(array(), $this->fixture->getTypes());

        $this->fixture->setTypes(array(1));

        $this->assertSame(array(1), $this->fixture->getTypes());
    }

    /**
     * @covers phpDocumentor\Descriptor\PropertyDescriptor::getDefault
     * @covers phpDocumentor\Descriptor\PropertyDescriptor::setDefault
     */
    public function testSetAndGetDefault()
    {
        $this->assertSame(null, $this->fixture->getDefault());

        $this->fixture->setDefault('a');

        $this->assertSame('a', $this->fixture->getDefault());
    }
}
