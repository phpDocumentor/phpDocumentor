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
use phpDocumentor\Descriptor\Validation\Error;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests the functionality for the DescriptorAbstract class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\DescriptorAbstract
 
 */
class DescriptorAbstractTest extends MockeryTestCase
{
    use ProphecyTrait;

    /** @var DescriptorAbstract $fixture */
    protected $fixture;

    /**
     * Creates a new mocked fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new TestSubjectDescriptor();
    }

    /** @covers ::__construct */
    public function testInitialize(): void
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
    public function testSettingAndGettingFullyQualifiedStructuralElementName(): void
    {
        self::assertSame(null, $this->fixture->getFullyQualifiedStructuralElementName());

        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor'));

        self::assertSame('\phpDocumentor', (string) $this->fixture->getFullyQualifiedStructuralElementName());
    }

    /**
     * @covers ::setName
     * @covers ::getName
     */
    public function testSettingAndGettingName(): void
    {
        self::assertSame('', $this->fixture->getName());

        $this->fixture->setName('name');

        self::assertSame('name', $this->fixture->getName());
    }

    /**
     * @covers ::setNamespace
     * @covers ::getNamespace
     */
    public function testSettingAndGettingNamespace(): void
    {
        self::assertEquals('', $this->fixture->getNamespace());

        $mock = m::mock(NamespaceDescriptor::class);

        $this->fixture->setNamespace($mock);

        self::assertSame($mock, $this->fixture->getNamespace());
    }

    /**
     * @covers ::setSummary
     * @covers ::getSummary
     */
    public function testSettingAndGettingSummary(): void
    {
        self::assertSame('', $this->fixture->getSummary());

        $this->fixture->setSummary('summary');

        self::assertSame('summary', $this->fixture->getSummary());
    }

    /** @covers ::getSummary */
    public function testSummaryInheritsWhenNoneIsPresent(): void
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
        self::assertSame($summary, $result);
    }

    /**
     * @covers ::setDescription
     * @covers ::getDescription
     */
    public function testSettingAndGettingDescription(): void
    {
        $description = new DescriptionDescriptor(new Description(''), []);
        self::assertEquals($description, $this->fixture->getDescription());
        self::assertNotSame($description, $this->fixture->getDescription());

        $description = new DescriptionDescriptor(new Description('description'), []);
        $this->fixture->setDescription($description);

        self::assertSame($description, $this->fixture->getDescription());
    }

    public function testWhenDescriptionIsNullParentDescriptionIsInherited(): void
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
    public function testSettingAndGettingPackage(): void
    {
        $package = new PackageDescriptor();
        self::assertNull($this->fixture->getPackage());

        $this->fixture->setPackage($package);

        self::assertSame($package, $this->fixture->getPackage());
    }

    /** @covers ::getAuthor */
    public function testGetAuthor(): void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = m::mock(
            DescriptorAbstract::class,
            ChildInterface::class,
        );
        $mock->makePartial();

        $author = new Collection(['author']);

        $collection = new Collection();
        $collection->offsetSet('author', $author);

        $mock->shouldReceive('getTags')->andReturn($collection);
        self::assertSame($author, $mock->getAuthor());
    }

    /** @covers ::getVersion */
    public function testGetVersion(): void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = m::mock(
            DescriptorAbstract::class,
            ChildInterface::class,
        );
        $mock->makePartial();

        $version = new Collection(['version']);

        $collection = new Collection();
        $collection->offsetSet('version', $version);

        $mock->shouldReceive('getTags')->andReturn($collection);
        self::assertSame($version, $mock->getVersion());
    }

    /** @covers ::getCopyright */
    public function testGetCopyRight(): void
    {
        /** @var m\MockInterface|DescriptorAbstract $mock */
        $mock = m::mock(
            DescriptorAbstract::class,
            ChildInterface::class,
        );
        $mock->makePartial();

        $copyright = new Collection(['copyright']);

        $collection = new Collection();
        $collection->offsetSet('copyright', $copyright);

        $mock->shouldReceive('getTags')->andReturn($collection);
        self::assertSame($copyright, $mock->getCopyright());
    }

    /**
     * @covers ::setLocation
     * @covers ::getFile
     * @covers ::getStartLocation
     */
    public function testSettingAndGettingLocation(): void
    {
        self::assertNull($this->fixture->getFile());
        self::assertSame(0, $this->fixture->getLine());

        $startLocation = new Location(5, 100);
        $this->fixture->setLocation(m::mock(FileDescriptor::class), $startLocation);

        self::assertInstanceOf(FileDescriptor::class, $this->fixture->getFile());
        self::assertSame($startLocation, $this->fixture->getStartLocation());
    }

    /**
     * @covers ::getStartLocation
     * @covers ::setStartLocation
     */
    public function testSettingAndGettingStartLocation(): void
    {
        self::assertNull($this->fixture->getStartLocation());

        $startLocation = new Location(10, 200);
        $this->fixture->setStartLocation($startLocation);

        self::assertSame($startLocation, $this->fixture->getStartLocation());
    }

    /**
     * @covers ::getEndLocation
     * @covers ::setEndLocation
     */
    public function testSettingAndGettingEndLocation(): void
    {
        self::assertNull($this->fixture->getEndLocation());

        $endLocation = new Location(99, 5200);
        $this->fixture->setEndLocation($endLocation);

        self::assertSame($endLocation, $this->fixture->getEndLocation());
    }

    /** @covers ::getPath */
    public function testGetPath(): void
    {
        self::assertSame('', $this->fixture->getPath());

        /** @var m\MockInterface $file */
        $file = m::mock(FileDescriptor::class);
        $file->shouldReceive('getPath')->andReturn('path');
        $this->fixture->setLocation($file, new Location(10));

        self::assertSame('path', $this->fixture->getPath());
    }

    /**
     * @covers ::setTags
     * @covers ::getTags
     */
    public function testSettingAndGettingTags(): void
    {
        /** @var Collection $mock */
        $mock = m::mock(Collection::class);
        $this->fixture->setTags($mock);

        self::assertSame($mock, $this->fixture->getTags());
    }

    /** @covers ::isDeprecated */
    public function testIsDeprecated(): void
    {
        self::assertFalse($this->fixture->isDeprecated());

        $this->fixture->setTags(new Collection(['deprecated' => 'deprecated']));

        self::assertTrue($this->fixture->isDeprecated());
    }

    /**
     * @covers ::setErrors
     * @covers ::getErrors
     */
    public function testSettingAndGettingErrors(): void
    {
        $tagError = new Error('error', 'myTag Error', 0);
        $descriptorError = new Error('error', 'myDescriptor Error', 10);

        $tagErrors = new Collection([$tagError]);
        $tagDescriptor = $this->prophesize(TagDescriptor::class);
        $tagDescriptor->getErrors()->willReturn($tagErrors);

        $this->fixture->setErrors(new Collection([$descriptorError]));
        $this->fixture->setTags(new Collection([new Collection([$tagDescriptor->reveal()])]));

        self::assertSame([$tagError, $descriptorError], $this->fixture->getErrors()->getAll());
    }

    /**
     * @covers ::setErrors
     * @covers ::getErrors
     */
    public function testErrorsInTagsAdoptElementsLineNumberWhenNoneIsAvailable(): void
    {
        $lineNumber = 10;
        $tagError = new Error('error', 'myTag Error', 0);

        $tagErrors = new Collection([$tagError]);
        $tagDescriptor = $this->prophesize(TagDescriptor::class);
        $tagDescriptor->getErrors()->willReturn($tagErrors);

        $this->fixture->setStartLocation(new Location($lineNumber));
        $this->fixture->setTags(new Collection([new Collection([$tagDescriptor->reveal()])]));

        self::assertSame($lineNumber, $this->fixture->getErrors()->first()->getLine());
    }

    /** @covers ::__toString */
    public function testToString(): void
    {
        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\Fqn'));
        self::assertSame('\Fqn', (string) $this->fixture);
    }
}
