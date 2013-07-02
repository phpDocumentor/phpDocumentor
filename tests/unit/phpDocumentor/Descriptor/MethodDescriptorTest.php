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
 * Tests the functionality for the MethodDescriptor class.
 */
class MethodDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var MethodDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new MethodDescriptor();
    }

    /**
     * Tests whether all collection objects are properly initialized.
     *
     * @covers phpDocumentor\Descriptor\MethodDescriptor::__construct
     */
    public function testInitialize()
    {
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'arguments', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::setArguments
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getArguments
     */
    public function testSettingAndGettingArguments()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getArguments());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setArguments($mock);

        $this->assertSame($mock, $this->fixture->getArguments());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::isAbstract
     * @covers phpDocumentor\Descriptor\MethodDescriptor::setAbstract
     */
    public function testSettingAndGettingWhetherMethodIsAbstract()
    {
        $this->assertFalse($this->fixture->isAbstract());

        $this->fixture->setAbstract(true);

        $this->assertTrue($this->fixture->isAbstract());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::isFinal
     * @covers phpDocumentor\Descriptor\MethodDescriptor::setFinal
     */
    public function testSettingAndGettingWhetherMethodIsFinal()
    {
        $this->assertFalse($this->fixture->isFinal());

        $this->fixture->setFinal(true);

        $this->assertTrue($this->fixture->isFinal());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::isStatic
     * @covers phpDocumentor\Descriptor\MethodDescriptor::setStatic
     */
    public function testSettingAndGettingWhetherMethodIsStatic()
    {
        $this->assertFalse($this->fixture->isStatic());

        $this->fixture->setStatic(true);

        $this->assertTrue($this->fixture->isStatic());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getVisibility
     * @covers phpDocumentor\Descriptor\MethodDescriptor::setVisibility
     */
    public function testSettingAndGettingVisibility()
    {
        $this->assertEquals('public', $this->fixture->getVisibility());

        $this->fixture->setVisibility('private');

        $this->assertEquals('private', $this->fixture->getVisibility());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getResponse
     */
    public function testRetrieveReturnTagForResponse()
    {
        $mock = new \stdClass();

        $this->assertNull($this->fixture->getResponse());

        $this->fixture->getTags()->set('return', new Collection(array($mock)));

        $this->assertSame($mock, $this->fixture->getResponse());
    }
}
