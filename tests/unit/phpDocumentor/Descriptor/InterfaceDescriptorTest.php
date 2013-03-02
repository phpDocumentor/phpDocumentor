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
 * Tests the functionality for the InterfaceDescriptor class.
 */
class InterfaceDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var InterfaceDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new InterfaceDescriptor();
    }

    /**
     * Tests whether all collection objects are properly initialized.
     *
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::__construct
     */
    public function testInitialize()
    {
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'extends', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'constants', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'methods', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::setParentInterfaces
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::getParentInterfaces
     */
    public function testSettingAndGettingParentInterfaces()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getParentInterfaces());

        $mock = &m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setParentInterfaces($mock);

        $this->assertSame($mock, $this->fixture->getParentInterfaces());
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::setConstants
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::getConstants
     */
    public function testSettingAndGettingConstants()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getConstants());

        $mock = &m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setConstants($mock);

        $this->assertSame($mock, $this->fixture->getConstants());
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::setMethods
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::getMethods
     */
    public function testSettingAndGettingMethods()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMethods());

        $mock = &m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::clearReferences
     */
    public function testClearingReferencesClearsMethodsAndConstants()
    {
        $constants = m::mock('phpDocumentor\Descriptor\Collection');
        $methods = clone $constants;
        $constants->shouldReceive('clearReferences')->once();
        $methods->shouldReceive('clearReferences')->once();

        $this->fixture->setConstants($constants);
        $this->fixture->setMethods($methods);

        // without an assert PHPUnit thinks this test is incomplete; but we only want to check mocks,
        // so we make a faux assert
        $this->assertNull($this->fixture->clearReferences());
    }
}
