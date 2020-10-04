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

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Interfaces\ChildInterface;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Fqsen;

/**
 * Tests the functionality for the DescriptorAbstract class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\DescriptorAbstract
 * @covers ::<private>
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
        $this->fixture = new TestSubjectDescriptor();
    }

    /**
     * @covers ::__construct
     */
    public function testInitialize() : void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = $this->getMockBuilder(DescriptorAbstract::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('setTags')->with(new Collection());
        $mock->expects($this->once())->method('setErrors')->with(new Collection());
        $mock->__construct();
    }

    /**
     * @covers ::setFullyQualifiedStructuralElementName
     * @covers ::getFullyQualifiedStructuralElementName
     */
    public function testSettingAndGettingFullyQualifiedStructuralElementName() : void
    {
        $this->assertSame(null, $this->fixture->getFullyQualifiedStructuralElementName());

        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor'));

        $this->assertSame('\phpDocumentor', (string) $this->fixture->getFullyQualifiedStructuralElementName());
    }

    /**
     * @covers ::setName
     * @covers ::getName
     */
    public function testSettingAndGettingName() : void
    {
        $this->assertSame('', $this->fixture->getName());

        $this->fixture->setName('name');

        $this->assertSame('name', $this->fixture->getName());
    }

    /**
     * @covers ::setNamespace
     * @covers ::getNamespace
     */
    public function testSettingAndGettingNamespace() : void
    {
        $this->assertEquals('', $this->fixture->getNamespace());

        $mock = m::mock(NamespaceDescriptor::class);

        $this->fixture->setNamespace($mock);

        $this->assertSame($mock, $this->fixture->getNamespace());
    }

    /**
     * @covers ::setSummary
     * @covers ::getSummary
     */
    public function testSettingAndGettingSummary() : void
    {
        $this->assertSame('', $this->fixture->getSummary());

        $this->fixture->setSummary('summary');

        $this->assertSame('summary', $this->fixture->getSummary());
    }

    /**
     * @covers ::getSummary
     */
    public function testSummaryInheritsWhenNoneIsPresent() : void
    {
        // Arrange
        $summary = 'This is a summary';
        $parent = new TestSubjectDescriptor();
        $parent->setSummary($summary);

        $this->fixture->setSummary('');
        $this->fixture->setParent($parent);

        // Act
        $result = $this->fixture->getSummary();

        // Assert
        $this->assertSame($summary, $result);
    }

    /**
     * @covers ::setDescription
     * @covers ::getDescription
     */
    public function testSettingAndGettingDescription() : void
    {
        $this->assertNull($this->fixture->getDescription());

        $description = new DescriptionDescriptor(new Description('description'), []);
        $this->fixture->setDescription($description);

        $this->assertSame($description, $this->fixture->getDescription());
    }

    public function testWhenDescriptionIsNullParentDescriptionIsInherited() : void
    {
        $parent = new TestSubjectDescriptor();
        $description = new DescriptionDescriptor(new Description('parent'), []);
        $parent->setDescription($description);
        $this->fixture->setParent($parent);

        self::assertSame($description, $this->fixture->getDescription());
    }

    /**
     * @covers ::setPackage
     * @covers ::getPackage
     */
    public function testSettingAndGettingPackage() : void
    {
        $package = new PackageDescriptor();
        $this->assertNull($this->fixture->getPackage());

        $this->fixture->setPackage($package);

        $this->assertSame($package, $this->fixture->getPackage());
    }

    /**
     * @covers ::getAuthor
     */
    public function testGetAuthor() : void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = m::mock(
            DescriptorAbstract::class,
            ChildInterface::class
        );
        $mock->shouldDeferMissing();

        $author = new Collection(['author']);

        $collection = new Collection();
        $collection->offsetSet('author', $author);

        $mock->shouldReceive('getTags')->andReturn($collection);
        $this->assertSame($author, $mock->getAuthor());
    }

    /**
     * @covers ::getVersion
     */
    public function testGetVersion() : void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = m::mock(
            DescriptorAbstract::class,
            ChildInterface::class
        );
        $mock->shouldDeferMissing();

        $version = new Collection(['version']);

        $collection = new Collection();
        $collection->offsetSet('version', $version);

        $mock->shouldReceive('getTags')->andReturn($collection);
        $this->assertSame($version, $mock->getVersion());
    }

    /**
     * @covers ::getCopyright
     */
    public function testGetCopyRight() : void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = m::mock(
            DescriptorAbstract::class,
            ChildInterface::class
        );
        $mock->shouldDeferMissing();

        $copyright = new Collection(['copyright']);

        $collection = new Collection();
        $collection->offsetSet('copyright', $copyright);

        $mock->shouldReceive('getTags')->andReturn($collection);
        $this->assertSame($copyright, $mock->getCopyright());
    }

    /**
     * @covers ::setLocation
     * @covers ::getFile
     * @covers ::getLine
     */
    public function testSettingAndGettingLocation() : void
    {
        $this->assertNull($this->fixture->getFile());
        $this->assertSame(0, $this->fixture->getLine());

        $this->fixture->setLocation(m::mock(FileDescriptor::class), 5);

        $this->assertInstanceOf(FileDescriptor::class, $this->fixture->getFile());
        $this->assertSame(5, $this->fixture->getLine());
    }

    /**
     * @covers ::setLine
     * @covers ::getLine
     */
    public function testSetLineNumber() : void
    {
        $this->assertSame(0, $this->fixture->getLine());

        $this->fixture->setLine(5);

        $this->assertSame(5, $this->fixture->getLine());
    }

    /**
     * @covers ::getPath
     */
    public function testGetPath() : void
    {
        $this->assertSame('', $this->fixture->getPath());

        /** @var m\MockInterface $file */
        $file = m::mock(FileDescriptor::class);
        $file->shouldReceive('getPath')->andReturn('path');
        $this->fixture->setLocation($file);

        $this->assertSame('path', $this->fixture->getPath());
    }

    /**
     * @covers ::setTags
     * @covers ::getTags
     */
    public function testSettingAndGettingTags() : void
    {
        /** @var Collection $mock */
        $mock = m::mock(Collection::class);
        $this->fixture->setTags($mock);

        $this->assertSame($mock, $this->fixture->getTags());
    }

    /**
     * @covers ::isDeprecated
     */
    public function testIsDeprecated() : void
    {
        $this->assertFalse($this->fixture->isDeprecated());

        $this->fixture->setTags(new Collection(['deprecated' => 'deprecated']));

        $this->assertTrue($this->fixture->isDeprecated());
    }

    /**
     * @covers ::setErrors
     * @covers ::getErrors
     */
    public function testSettingAndGettingErrors() : void
    {
        $tagErrors = new Collection(['myTag Error']);
        $tagDescriptor = $this->prophesize(TagDescriptor::class);
        $tagDescriptor->getErrors()->willReturn($tagErrors);

        $this->fixture->setErrors(new Collection(['myDescriptor Error']));
        $this->fixture->setTags(new Collection([new Collection([$tagDescriptor->reveal()])]));

        $this->assertEquals(
            [
                'myDescriptor Error',
                'myTag Error',
            ],
            $this->fixture->getErrors()->getAll()
        );
    }

    /**
     * @covers ::__toString
     */
    public function testToString() : void
    {
        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\Fqn'));
        $this->assertSame('\Fqn', (string) $this->fixture);
    }
}
