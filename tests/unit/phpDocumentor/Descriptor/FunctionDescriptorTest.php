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
 * Tests the functionality for the FunctionDescriptor class.
 */
class FunctionDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var FunctionDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new FunctionDescriptor();
    }

    /**
     * Tests whether all collection objects are properly initialized.
     *
     * @covers phpDocumentor\Descriptor\FunctionDescriptor::__construct
     */
    public function testInitialize()
    {
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'arguments', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\FunctionDescriptor::setArguments
     * @covers phpDocumentor\Descriptor\FunctionDescriptor::getArguments
     */
    public function testSettingAndGettingArguments()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getArguments());

        $mockInstance = m::mock('phpDocumentor\Descriptor\Collection');
        $mock = &$mockInstance;

        $this->fixture->setArguments($mock);

        $this->assertSame($mockInstance, $this->fixture->getArguments());
    }
}
