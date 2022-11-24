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
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\String_;

/**
 * Tests the functionality for the ConstantDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\ConstantDescriptor
 * @covers ::__construct
 * @covers ::<private>
 */
final class ConstantDescriptorTest extends MockeryTestCase
{
    private ConstantDescriptor $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new ConstantDescriptor();
        $this->fixture->setNamespace('\My\Namespace');
        $this->fixture->setName('CONSTANT');
    }

    /**
     * @covers ::getParent
     * @covers ::setParent
     */
    public function testSetAndGetParentClass(): void
    {
        self::assertNull($this->fixture->getParent());

        $parentMock = m::mock(ClassDescriptor::class);
        $parentMock->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\TestClass'));

        $this->fixture->setParent($parentMock);

        self::assertSame($parentMock, $this->fixture->getParent());
    }

    /**
     * @covers ::getParent
     * @covers ::setParent
     */
    public function testSetAndGetParentInterface(): void
    {
        self::assertNull($this->fixture->getParent());

        $parentMock = m::mock(InterfaceDescriptor::class);
        $parentMock->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\TestInterface'));
        $this->fixture->setParent($parentMock);

        self::assertSame($parentMock, $this->fixture->getParent());
    }

    /**
     * @covers ::getType
     * @covers ::getTypes
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
     * @covers ::getTypes
     * @covers ::getVar
     */
    public function testDeterminingTypeDerivedFromVarTag(): void
    {
        $expected = new String_();

        $varTag = new VarDescriptor('var');
        $varTag->setType($expected);

        $this->fixture->getTags()->set('var', new Collection([$varTag]));

        self::assertEquals($expected, $this->fixture->getType());
    }

    /**
     * @covers ::getVar
     */
    public function testEmptyCollectionIsReturnedWhenNoVarTagsPresent(): void
    {
        self::assertEquals(new Collection(), $this->fixture->getVar());
    }

    /**
     * @covers ::getInheritedElement
     * @covers ::getTypes
     * @covers ::getVar
     */
    public function testGetTypesUsingInheritanceOfVarTag(): void
    {
        $expected = new String_();

        $constantName = 'CONSTANT';
        $this->fixture->setName($constantName);
        $parentClass = $this->createParentClassWithSuperClassAndConstant($expected, $constantName);

        // Attempt to get the types; which come from the superclass' constants
        $this->fixture->setParent($parentClass);
        $types = $this->fixture->getType();

        self::assertSame($expected, $types);
    }

    /**
     * @covers ::getValue
     * @covers ::setValue
     */
    public function testSetAndGetValue(): void
    {
        self::assertEmpty($this->fixture->getValue());

        $this->fixture->setValue('a');

        self::assertSame('a', $this->fixture->getValue());
    }

    /**
     * @covers ::getFile
     */
    public function testRetrieveFileAssociatedWithAGlobalConstant(): void
    {
        // Arrange
        $file = $this->whenFixtureIsDirectlyRelatedToAFile();

        // Act
        $result = $this->fixture->getFile();

        // Assert
        self::assertSame($file, $result);
    }

    /**
     * @covers ::getFile
     */
    public function testRetrieveFileAssociatedWithAClassConstant(): void
    {
        // Arrange
        $file = $this->whenFixtureIsRelatedToAClassWithFile();

        // Act
        $result = $this->fixture->getFile();

        // Assert
        self::assertSame($file, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getSummary
     */
    public function testSummaryInheritsWhenNoneIsPresent(): void
    {
        // Arrange
        $summary = 'This is a summary';
        $this->fixture->setSummary('');
        $parentConstant = $this->whenFixtureHasConstantInParentClassWithSameName($this->fixture->getName());
        $parentConstant->setSummary($summary);

        // Act
        $result = $this->fixture->getSummary();

        // Assert
        self::assertSame($summary, $result);
    }

    /**
     * @covers ::getVar
     */
    public function testVarTagsInheritWhenNoneArePresent(): void
    {
        // Arrange
        $varTagDescriptor = new VarDescriptor('var');
        $varCollection = new Collection([$varTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasConstantInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('var', $varCollection);

        // Act
        $result = $this->fixture->getVar();

        // Assert
        self::assertSame($varCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getPackage
     */
    public function testPackageInheritWhenNoneArePresent(): void
    {
        // Arrange
        $packageTagDescriptor = new PackageDescriptor();
        $this->fixture->setPackage('');
        $parentProperty = $this->whenFixtureHasConstantInParentClassWithSameName($this->fixture->getName());
        $parentProperty->setPackage($packageTagDescriptor);

        // Act
        $result = $this->fixture->getPackage();

        // Assert
        self::assertSame($packageTagDescriptor, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getAuthor
     */
    public function testAuthorTagsInheritWhenNoneArePresent(): void
    {
        // Arrange
        $authorTagDescriptor = new AuthorDescriptor('author');
        $authorCollection = new Collection([$authorTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasConstantInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('author', $authorCollection);

        // Act
        $result = $this->fixture->getAuthor();

        // Assert
        self::assertSame($authorCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getVersion
     */
    public function testVersionTagsInheritWhenNoneArePresent(): void
    {
        // Arrange
        $versionTagDescriptor = new VersionDescriptor('version');
        $versionCollection = new Collection([$versionTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasConstantInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('version', $versionCollection);

        // Act
        $result = $this->fixture->getVersion();

        // Assert
        self::assertSame($versionCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getCopyright
     */
    public function testCopyrightTagsInheritWhenNoneArePresent(): void
    {
        // Arrange
        $copyrightTagDescriptor = new TagDescriptor('copyright');
        $copyrightCollection = new Collection([$copyrightTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasConstantInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('copyright', $copyrightCollection);

        // Act
        $result = $this->fixture->getCopyright();

        // Assert
        self::assertSame($copyrightCollection, $result);
    }

    /**
     * Creates a parentClass for a Constant with a SuperClass, which in turn has a constant exposing the given types.
     *
     * The created ParentClass can be used to test the inheritance of properties of a constant descriptor, such as
     * inheriting type information.
     *
     * @return m\MockInterface|ClassDescriptor
     */
    protected function createParentClassWithSuperClassAndConstant(Type $type, string $constantName)
    {
        // construct the to-be-inherited constant and its @var tag
        $varTag = m::mock(VarDescriptor::class);
        $varTag->shouldReceive('getType')->andReturn($type);

        $parentConstant = m::mock(ConstantDescriptor::class);
        $parentConstant->shouldReceive('getVar')->andReturn(new Collection([$varTag]));

        // create SuperClassMock and add a Constant collection with out to-be-inherited constant
        $superClass = m::mock(ClassDescriptor::class);
        $superClass->shouldReceive('getConstants')->andReturn(
            new Collection(
                [$constantName => $parentConstant]
            )
        );

        // create and set the parent class for our fixture
        $parentClass = m::mock(ClassDescriptor::class);
        $parentClass->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\TestClass'));
        $parentClass->shouldReceive('getParent')->andReturn($superClass);

        return $parentClass;
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
        $parent->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\Class1'));
        $this->fixture->setParent($parent);

        return $file;
    }

    /**
     * @param string $name The name of the current constant.
     */
    protected function whenFixtureHasConstantInParentClassWithSameName(string $name): ConstantDescriptor
    {
        $result = new ConstantDescriptor();
        $result->setNamespace('\My\Namespace');
        $result->setName($name);

        $parent = new ClassDescriptor();
        $parent->setNamespace('\My\Namespace');
        $parent->setFullyQualifiedStructuralElementName(new Fqsen('\My\Super\Class'));
        $result->setParent($parent);
        $parent->getConstants()->set($name, $result);

        $class = new ClassDescriptor();
        $class->setNamespace('\My\Namespace');
        $class->setFullyQualifiedStructuralElementName(new Fqsen('\My\Class'));
        $class->setParent($parent);

        $this->fixture->setParent($class);

        return $result;
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
}
