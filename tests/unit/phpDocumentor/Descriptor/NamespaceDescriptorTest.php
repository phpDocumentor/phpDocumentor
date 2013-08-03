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
 * Tests the functionality for the NamespaceDescriptor class.
 */
class NamespaceDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var NamespaceDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new NamespaceDescriptor();
    }

    /**
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::getParent
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::setParent
     */
    public function testSetAndGetParent()
    {
        $parent = new NamespaceDescriptor();

        $this->assertSame(null, $this->fixture->getParent());

        $this->fixture->setParent($parent);

        $this->assertSame($parent, $this->fixture->getParent());
    }

    /**
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::getClasses
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::setClasses
     */
    public function testSetAndGetClasses()
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getClasses());

        $this->fixture->setClasses($collection);

        $this->assertSame($collection, $this->fixture->getClasses());
    }

    /**
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::getConstants
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::setConstants
     */
    public function testSetAndGetConstants()
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getConstants());

        $this->fixture->setConstants($collection);

        $this->assertSame($collection, $this->fixture->getConstants());
    }

    /**
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::getFunctions
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::setFunctions
     */
    public function testSetAndGetFunctions()
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getFunctions());

        $this->fixture->setFunctions($collection);

        $this->assertSame($collection, $this->fixture->getFunctions());
    }

    /**
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::getInterfaces
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::setInterfaces
     */
    public function testSetAndGetInterfaces()
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getInterfaces());

        $this->fixture->setInterfaces($collection);

        $this->assertSame($collection, $this->fixture->getInterfaces());
    }

    /**
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::getChildren
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::setChildren
     */
    public function testSetAndGetChildren()
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getChildren());

        $this->fixture->setChildren($collection);

        $this->assertSame($collection, $this->fixture->getChildren());
    }

    /**
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::getTraits
     * @covers phpDocumentor\Descriptor\NamespaceDescriptor::setTraits
     */
    public function testSetAndGetTraits()
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getTraits());

        $this->fixture->setTraits($collection);

        $this->assertSame($collection, $this->fixture->getTraits());
    }
}
