<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */
namespace phpDocumentor\Descriptor;

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the functionality for the NamespaceDescriptor class.
 */
class NamespaceDescriptorTest extends MockeryTestCase
{
    /** @var NamespaceDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new NamespaceDescriptor();
    }

    /**
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::getParent
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::setParent
     */
    public function testSetAndGetParent() : void
    {
        $parent = new NamespaceDescriptor();

        $this->assertNull($this->fixture->getParent());

        $this->fixture->setParent($parent);

        $this->assertSame($parent, $this->fixture->getParent());
    }

    /**
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::getClasses
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::setClasses
     */
    public function testSetAndGetClasses() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getClasses());

        $this->fixture->setClasses($collection);

        $this->assertSame($collection, $this->fixture->getClasses());
    }

    /**
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::getConstants
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::setConstants
     */
    public function testSetAndGetConstants() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getConstants());

        $this->fixture->setConstants($collection);

        $this->assertSame($collection, $this->fixture->getConstants());
    }

    /**
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::getFunctions
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::setFunctions
     */
    public function testSetAndGetFunctions() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getFunctions());

        $this->fixture->setFunctions($collection);

        $this->assertSame($collection, $this->fixture->getFunctions());
    }

    /**
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::getInterfaces
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::setInterfaces
     */
    public function testSetAndGetInterfaces() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getInterfaces());

        $this->fixture->setInterfaces($collection);

        $this->assertSame($collection, $this->fixture->getInterfaces());
    }

    /**
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::getChildren
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::setChildren
     */
    public function testSetAndGetChildren() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getChildren());

        $this->fixture->setChildren($collection);

        $this->assertSame($collection, $this->fixture->getChildren());
    }

    /**
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::__construct
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::getTraits
     * @covers \phpDocumentor\Descriptor\NamespaceDescriptor::setTraits
     */
    public function testSetAndGetTraits() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getTraits());

        $this->fixture->setTraits($collection);

        $this->assertSame($collection, $this->fixture->getTraits());
    }
}
