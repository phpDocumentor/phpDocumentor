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

use \Mockery as m;

/**
 * Tests the functionality for the ConstantDescriptor class.
 */
class ConstantDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ConstantDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ConstantDescriptor();
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getTypes
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::setTypes
     */
    public function testSetAndGetTypes()
    {
        $this->assertSame(array(), $this->fixture->getTypes());

        $this->fixture->setTypes(array(1));

        $this->assertSame(array(1), $this->fixture->getTypes());
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getValue
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::setValue
     */
    public function testSetAndGetValue()
    {
        $this->assertSame(null, $this->fixture->getValue());

        $this->fixture->setValue('a');

        $this->assertSame('a', $this->fixture->getValue());
    }
}
