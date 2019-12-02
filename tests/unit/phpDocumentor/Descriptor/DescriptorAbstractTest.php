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

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Reflection\Fqsen;

/**
 * Tests the functionality for the DescriptorAbstract class.
 */
class DescriptorAbstractTest extends MockeryTestCase
{
    /** @var DescriptorAbstract $fixture */
    protected $fixture;

    /**
     * Creates a new mocked fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $this->fixture->shouldDeferMissing();
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::__construct
     */
    public function testInitialize() : void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = $this->getMockBuilder('phpDocumentor\Descriptor\DescriptorAbstract')
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('setTags')->with(new Collection());
        $mock->expects($this->once())->method('setErrors')->with(new Collection());
        $mock->__construct();
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::setFullyQualifiedStructuralElementName
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getFullyQualifiedStructuralElementName
     */
    public function testSettingAndGettingFullyQualifiedStructuralElementName() : void
    {
        $this->assertSame(null, $this->fixture->getFullyQualifiedStructuralElementName());

        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor'));

        $this->assertSame('\phpDocumentor', (string) $this->fixture->getFullyQualifiedStructuralElementName());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::setName
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getName
     */
    public function testSettingAndGettingName() : void
    {
        $this->assertSame('', $this->fixture->getName());

        $this->fixture->setName('name');

        $this->assertSame('name', $this->fixture->getName());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::setNamespace
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getNamespace
     */
    public function testSettingAndGettingNamespace() : void
    {
        $this->assertEquals('', $this->fixture->getNamespace());

        $mock = m::mock('phpDocumentor\Descriptor\NamespaceDescriptor');

        $this->fixture->setNamespace($mock);

        $this->assertSame($mock, $this->fixture->getNamespace());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::setSummary
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getSummary
     */
    public function testSettingAndGettingSummary() : void
    {
        $this->assertSame('', $this->fixture->getSummary());

        $this->fixture->setSummary('summary');

        $this->assertSame('summary', $this->fixture->getSummary());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::setDescription
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getDescription
     */
    public function testSettingAndGettingDescription() : void
    {
        $this->assertSame('', $this->fixture->getDescription());

        $this->fixture->setDescription('description');

        $this->assertSame('description', $this->fixture->getDescription());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::setPackage
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getPackage
     */
    public function testSettingAndGettingPackage() : void
    {
        $package = new PackageDescriptor();
        $this->assertNull($this->fixture->getPackage());

        $this->fixture->setPackage($package);

        $this->assertSame($package, $this->fixture->getPackage());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getAuthor
     */
    public function testGetAuthor() : void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = m::mock(
            'phpDocumentor\Descriptor\DescriptorAbstract, phpDocumentor\Descriptor\Interfaces\ChildInterface'
        );
        $mock->shouldDeferMissing();

        $author = new Collection(['author']);

        $collection = new Collection();
        $collection->offsetSet('author', $author);

        $mock->shouldReceive('getTags')->andReturn($collection);
        $this->assertSame($author, $mock->getAuthor());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getVersion
     */
    public function testGetVersion() : void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = m::mock(
            'phpDocumentor\Descriptor\DescriptorAbstract, phpDocumentor\Descriptor\Interfaces\ChildInterface'
        );
        $mock->shouldDeferMissing();

        $version = new Collection(['version']);

        $collection = new Collection();
        $collection->offsetSet('version', $version);

        $mock->shouldReceive('getTags')->andReturn($collection);
        $this->assertSame($version, $mock->getVersion());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getCopyright
     */
    public function testGetCopyRight() : void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = m::mock(
            'phpDocumentor\Descriptor\DescriptorAbstract, phpDocumentor\Descriptor\Interfaces\ChildInterface'
        );
        $mock->shouldDeferMissing();

        $copyright = new Collection(['copyright']);

        $collection = new Collection();
        $collection->offsetSet('copyright', $copyright);

        $mock->shouldReceive('getTags')->andReturn($collection);
        $this->assertSame($copyright, $mock->getCopyright());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::setLocation
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getFile
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getLine
     */
    public function testSettingAndGettingLocation() : void
    {
        $this->assertNull($this->fixture->getFile());
        $this->assertSame(0, $this->fixture->getLine());

        $this->fixture->setLocation(m::mock('phpDocumentor\Descriptor\FileDescriptor'), 5);

        $this->assertInstanceOf('phpDocumentor\Descriptor\FileDescriptor', $this->fixture->getFile());
        $this->assertSame(5, $this->fixture->getLine());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::setLine
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getLine
     */
    public function testSetLineNumber() : void
    {
        $this->assertSame(0, $this->fixture->getLine());

        $this->fixture->setLine(5);

        $this->assertSame(5, $this->fixture->getLine());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getPath
     */
    public function testGetPath() : void
    {
        $this->assertSame('', $this->fixture->getPath());

        /** @var m\MockInterface $file */
        $file = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $file->shouldReceive('getPath')->andReturn('path');
        $this->fixture->setLocation($file);

        $this->assertSame('path', $this->fixture->getPath());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::setTags
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getTags
     */
    public function testSettingAndGettingTags() : void
    {
        /** @var Collection $mock */
        $mock = m::mock('phpDocumentor\Descriptor\Collection');
        $this->fixture->setTags($mock);

        $this->assertSame($mock, $this->fixture->getTags());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::isDeprecated
     */
    public function testIsDeprecated() : void
    {
        $this->assertFalse($this->fixture->isDeprecated());

        $this->fixture->setTags(new Collection(['deprecated' => 'deprecated']));

        $this->assertTrue($this->fixture->isDeprecated());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::setErrors
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getErrors
     */
    public function testSettingAndGettingErrors() : void
    {
        /** @var Collection $mock */
        $mock = m::mock('phpDocumentor\Descriptor\Collection');
        $this->fixture->setErrors($mock);

        $this->assertSame($mock, $this->fixture->getErrors());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::__toString
     */
    public function testToString() : void
    {
        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\Fqn'));
        $this->assertSame('\Fqn', (string) $this->fixture);
    }
}
