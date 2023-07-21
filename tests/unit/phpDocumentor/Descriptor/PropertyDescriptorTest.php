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
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Array_;

/**
 * Tests the functionality for the PropertyDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\PropertyDescriptor
 */
final class PropertyDescriptorTest extends MockeryTestCase
{
    protected PropertyDescriptor $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new PropertyDescriptor();
        $this->fixture->setName('property');
    }

    /**
     * @covers ::isStatic
     * @covers ::setStatic
     */
    public function testSettingAndGettingWhetherPropertyIsStatic(): void
    {
        self::assertFalse($this->fixture->isStatic());

        $this->fixture->setStatic(true);

        self::assertTrue($this->fixture->isStatic());
    }

    /**
     * @covers ::isReadOnly
     * @covers ::setReadOnly
     */
    public function testSettingAndGettingWhetherPropertyIsReadOnly(): void
    {
        self::assertFalse($this->fixture->isReadOnly());

        $this->fixture->setReadOnly(true);

        self::assertTrue($this->fixture->isReadOnly());
    }

    /**
     * @covers ::isWriteOnly
     * @covers ::setWriteOnly
     */
    public function testSettingAndGettingWhetherPropertyIsWriteOnly(): void
    {
        self::assertFalse($this->fixture->isWriteOnly());

        $this->fixture->setWriteOnly(true);

        self::assertTrue($this->fixture->isWriteOnly());
    }

    /**
     * @covers ::getVisibility
     * @covers ::setVisibility
     */
    public function testSettingAndGettingVisibility(): void
    {
        self::assertEquals('public', $this->fixture->getVisibility());

        $this->fixture->setVisibility('private');

        self::assertEquals('private', $this->fixture->getVisibility());
    }

    /**
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetTypes(): void
    {
        self::assertEquals(null, $this->fixture->getType());
        $expected = new Array_();

        $this->fixture->setType($expected);

        self::assertSame($expected, $this->fixture->getType());
    }

    /**
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetTypesWhenVarIsPresent(): void
    {
        // Arrange
        $typesCollection = new Array_();
        $varTagDescriptor = new VarDescriptor('var');
        $varTagDescriptor->setType($typesCollection);
        $varCollection = new Collection([$varTagDescriptor]);
        $this->fixture->getTags()->clear();
        $this->fixture->getTags()->set('var', $varCollection);

        // Act
        $result = $this->fixture->getType();

        // Assert
        self::assertSame($typesCollection, $result);
    }

    /**
     * @covers ::getDefault
     * @covers ::setDefault
     */
    public function testSetAndGetDefault(): void
    {
        self::assertNull($this->fixture->getDefault());

        $this->fixture->setDefault('a');

        self::assertSame('a', $this->fixture->getDefault());
    }

    /** @covers ::getFile */
    public function testRetrieveFileAssociatedWithAProperty(): void
    {
        // Arrange
        $file = $this->whenFixtureIsRelatedToAClassWithFile();

        // Act
        $result = $this->fixture->getFile();

        // Assert
        self::assertSame($file, $result);
    }

    /** @covers ::getVar */
    public function testVarTagsInheritWhenNoneArePresent(): void
    {
        // Arrange
        $varTagDescriptor = new VarDescriptor('var');
        $varCollection = new Collection([$varTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('var', $varCollection);

        // Act
        $result = $this->fixture->getVar();

        // Assert
        self::assertSame($varCollection, $result);
    }

    /** @covers ::getVar */
    public function testVarTagsWhenNoneArePresent(): void
    {
        $varCollection = new Collection();
        $result = $this->fixture->getVar();

        self::assertEquals($varCollection, $result);
    }

    /**
     * @covers ::getAuthor
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getAuthor
     */
    public function testAuthorTagsInheritWhenNoneArePresent(): void
    {
        // Arrange
        $authorTagDescriptor = new AuthorDescriptor('author');
        $authorCollection = new Collection([$authorTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('author', $authorCollection);

        // Act
        $result = $this->fixture->getAuthor();

        // Assert
        self::assertSame($authorCollection, $result);
    }

    /**
     * @covers ::getVersion
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getVersion
     */
    public function testVersionTagsInheritWhenNoneArePresent(): void
    {
        // Arrange
        $versionTagDescriptor = new VersionDescriptor('version');
        $versionCollection = new Collection([$versionTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('version', $versionCollection);

        // Act
        $result = $this->fixture->getVersion();

        // Assert
        self::assertSame($versionCollection, $result);
    }

    /**
     * @covers ::getCopyright
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getCopyright
     */
    public function testCopyrightTagsInheritWhenNoneArePresent(): void
    {
        // Arrange
        $copyrightTagDescriptor = new TagDescriptor('copyright');
        $copyrightCollection = new Collection([$copyrightTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('copyright', $copyrightCollection);

        // Act
        $result = $this->fixture->getCopyright();

        // Assert
        self::assertSame($copyrightCollection, $result);
    }

    /** @covers ::setParent */
    public function testFqsenHasDollarSignWhenParentIsSet(): void
    {
        $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        self::assertSame(
            '\MyOther\Class::$property',
            (string) $this->fixture->getFullyQualifiedStructuralElementName(),
        );
    }

    /**
     * @covers ::setParent
     * @covers ::getParent
     */
    public function testSettingAndGettingAParent(): void
    {
        $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        self::assertInstanceOf(ClassDescriptor::class, $this->fixture->getParent());
    }

    /** @covers ::getInheritedElement */
    public function testGettingAnInheritedElement(): void
    {
        $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());

        $inheritedProperty = $this->fixture->getInheritedElement();

        self::assertSame($inheritedProperty->getName(), $this->fixture->getName());
    }

    /** @covers ::getInheritedElement */
    public function testGettingAnInheritedElementWhenThereIsNone(): void
    {
        self::assertNull($this->fixture->getInheritedElement());
    }

    /**
     * Sets up mocks as such that the fixture has a file.
     *
     * @return m\MockInterface|FileDescriptor
     */
    protected function whenFixtureIsDirectlyRelatedToAFile()
    {
        $file = m::mock(FileDescriptor::class);
        $this->fixture->setFile($file);

        return $file;
    }

    /**
     * Sets up mocks as such that the fixture has a parent class, with a file.
     *
     * @return m\MockInterface|FileDescriptor
     */
    protected function whenFixtureIsRelatedToAClassWithFile()
    {
        $file = m::mock(FileDescriptor::class);
        $parent = m::mock(ClassDescriptor::class);
        $parent->shouldReceive('getFile')->andReturn($file);
        $parent->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn(new Fqsen('\Class1'));
        $this->fixture->setParent($parent);

        return $file;
    }

    /** @param string $name The name of the current property. */
    protected function whenFixtureHasPropertyInParentClassWithSameName(string $name): PropertyDescriptor
    {
        $parent = new ClassDescriptor();
        $parent->setFullyQualifiedStructuralElementName(new Fqsen('\MyClass'));

        $result = new PropertyDescriptor();
        $result->setName($name);
        $result->setParent($parent);

        $parent->getProperties()->set($name, $result);

        $class = new ClassDescriptor();
        $class->setFullyQualifiedStructuralElementName(new Fqsen('\MyOther\Class'));
        $class->setParent($parent);

        $this->fixture->setParent($class);

        return $result;
    }
}
