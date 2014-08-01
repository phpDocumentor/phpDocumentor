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
 * Tests the functionality for the ArgumentDescriptor class.
 */
class ArgumentDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArgumentDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ArgumentDescriptor();
    }

    /**
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::getTypes
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::setTypes
     */
    public function testSetAndGetTypes()
    {
        $this->assertSame(array(), $this->fixture->getTypes());

        $this->fixture->setTypes(array(1));

        $this->assertSame(array(1), $this->fixture->getTypes());
    }

    /**
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::getDefault
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::setDefault
     */
    public function testSetAndGetDefault()
    {
        $this->assertSame(null, $this->fixture->getDefault());

        $this->fixture->setDefault('a');

        $this->assertSame('a', $this->fixture->getDefault());
    }

    /**
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::isByReference
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::setByReference
     */
    public function testSetAndGetWhetherArgumentIsPassedByReference()
    {
        $this->assertSame(false, $this->fixture->isByReference());

        $this->fixture->setByReference(true);

        $this->assertSame(true, $this->fixture->isByReference());
    }
}
