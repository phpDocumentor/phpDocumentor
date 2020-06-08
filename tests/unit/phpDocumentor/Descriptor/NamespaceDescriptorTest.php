<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the NamespaceDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\NamespaceDescriptor
 */
final class NamespaceDescriptorTest extends TestCase
{
    /** @var NamespaceDescriptor $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new NamespaceDescriptor();
    }

    /**
     * @covers ::__construct
     * @covers ::getParent
     * @covers ::setParent
     */
    public function testSetAndGetParent() : void
    {
        $parent = new NamespaceDescriptor();

        $this->assertNull($this->fixture->getParent());

        $this->fixture->setParent($parent);

        $this->assertSame($parent, $this->fixture->getParent());
    }

    /**
     * @covers ::__construct
     * @covers ::getClasses
     * @covers ::setClasses
     */
    public function testSetAndGetClasses() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf(Collection::class, $this->fixture->getClasses());

        $this->fixture->setClasses($collection);

        $this->assertSame($collection, $this->fixture->getClasses());
    }

    /**
     * @covers ::__construct
     * @covers ::getConstants
     * @covers ::setConstants
     */
    public function testSetAndGetConstants() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf(Collection::class, $this->fixture->getConstants());

        $this->fixture->setConstants($collection);

        $this->assertSame($collection, $this->fixture->getConstants());
    }

    /**
     * @covers ::__construct
     * @covers ::getFunctions
     * @covers ::setFunctions
     */
    public function testSetAndGetFunctions() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf(Collection::class, $this->fixture->getFunctions());

        $this->fixture->setFunctions($collection);

        $this->assertSame($collection, $this->fixture->getFunctions());
    }

    /**
     * @covers ::__construct
     * @covers ::getInterfaces
     * @covers ::setInterfaces
     */
    public function testSetAndGetInterfaces() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf(Collection::class, $this->fixture->getInterfaces());

        $this->fixture->setInterfaces($collection);

        $this->assertSame($collection, $this->fixture->getInterfaces());
    }

    /**
     * @covers ::__construct
     * @covers ::getChildren
     * @covers ::setChildren
     */
    public function testSetAndGetChildren() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf(Collection::class, $this->fixture->getChildren());

        $this->fixture->setChildren($collection);

        $this->assertSame($collection, $this->fixture->getChildren());
    }

    /**
     * @covers ::__construct
     * @covers ::getTraits
     * @covers ::setTraits
     */
    public function testSetAndGetTraits() : void
    {
        $collection = new Collection();

        $this->assertInstanceOf(Collection::class, $this->fixture->getTraits());

        $this->fixture->setTraits($collection);

        $this->assertSame($collection, $this->fixture->getTraits());
    }
}
