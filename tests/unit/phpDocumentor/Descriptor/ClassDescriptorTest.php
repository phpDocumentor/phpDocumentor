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
 * Tests the functionality for the ClassDescriptor class.
 */
class ClassDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ClassDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ClassDescriptor();
    }

    /**
     * Tests whether all collection objects are properly initialized.
     *
     * @covers phpDocumentor\Descriptor\ClassDescriptor::__construct
     */
    public function testInitialize()
    {
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'implements', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'constants', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'properties', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'methods', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\ClassDescriptor::setParentClass
     * @covers phpDocumentor\Descriptor\ClassDescriptor::getParentClass
     */
    public function testSettingAndGettingAParentClass()
    {
        $this->assertNull($this->fixture->getParentClass());

        $mock = &m::mock('phpDocumentor\Descriptor\ClassDescriptor');

        $this->fixture->setParentClass($mock);

        $this->assertSame($mock, $this->fixture->getParentClass());

        // test if it is really a reference
        $mock = null;
        $this->assertNull($this->fixture->getParentClass());
    }

    /**
     * @covers phpDocumentor\Descriptor\ClassDescriptor::setInterfaces
     * @covers phpDocumentor\Descriptor\ClassDescriptor::getInterfaces
     */
    public function testSettingAndGettingInterfaces()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getInterfaces());

        $mock = &m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setInterfaces($mock);

        $this->assertSame($mock, $this->fixture->getInterfaces());
    }

    /**
     * @covers phpDocumentor\Descriptor\ClassDescriptor::setConstants
     * @covers phpDocumentor\Descriptor\ClassDescriptor::getConstants
     */
    public function testSettingAndGettingConstants()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getConstants());

        $mock = &m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setConstants($mock);

        $this->assertSame($mock, $this->fixture->getConstants());
    }

    /**
     * @covers phpDocumentor\Descriptor\ClassDescriptor::setProperties
     * @covers phpDocumentor\Descriptor\ClassDescriptor::getProperties
     */
    public function testSettingAndGettingProperties()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getProperties());

        $mock = &m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setProperties($mock);

        $this->assertSame($mock, $this->fixture->getProperties());
    }

    /**
     * @covers phpDocumentor\Descriptor\ClassDescriptor::setMethods
     * @covers phpDocumentor\Descriptor\ClassDescriptor::getMethods
     */
    public function testSettingAndGettingMethods()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMethods());

        $mock = &m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    /**
     * @covers phpDocumentor\Descriptor\ClassDescriptor::isAbstract
     * @covers phpDocumentor\Descriptor\ClassDescriptor::setAbstract
     */
    public function testSettingAndGettingWhetherClassIsAbstract()
    {
        $this->assertFalse($this->fixture->isAbstract());

        $this->fixture->setAbstract(true);

        $this->assertTrue($this->fixture->isAbstract());
    }

    /**
     * @covers phpDocumentor\Descriptor\ClassDescriptor::isFinal
     * @covers phpDocumentor\Descriptor\ClassDescriptor::setFinal
     */
    public function testSettingAndGettingWhetherClassIsFinal()
    {
        $this->assertFalse($this->fixture->isFinal());

        $this->fixture->setFinal(true);

        $this->assertTrue($this->fixture->isFinal());
    }

    /**
     * @covers phpDocumentor\Descriptor\ClassDescriptor::clearReferences
     */
    public function testClearingReferencesClearsMethodsConstantsAndProperties()
    {
        $constants = m::mock('phpDocumentor\Descriptor\Collection');
        $properties = clone $constants;
        $methods = clone $constants;
        $constants->shouldReceive('clearReferences')->once();
        $properties->shouldReceive('clearReferences')->once();
        $methods->shouldReceive('clearReferences')->once();

        $this->fixture->setConstants($constants);
        $this->fixture->setMethods($methods);
        $this->fixture->setProperties($properties);

        // without an assert PHPUnit thinks this test is incomplete; but we only want to check mocks,
        // so we make a faux assert
        $this->assertNull($this->fixture->clearReferences());
    }
}
