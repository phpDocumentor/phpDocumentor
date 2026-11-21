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

use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
use phpDocumentor\Descriptor\ValueObjects\Visibility;
use phpDocumentor\Descriptor\ValueObjects\VisibilityModifier;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests the functionality for the ConstantDescriptor class.
 */
#[CoversClass(ConstantDescriptor::class)]
#[CoversClass(DescriptorAbstract::class)]
final class ConstantDescriptorTest extends TestCase
{
    use ProphecyTrait;

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

    public function testSetAndGetParentClass(): void
    {
        self::assertNull($this->fixture->getParent());

        $parentMock = new ClassDescriptor();
        $parentMock->setFullyQualifiedStructuralElementName(new Fqsen('\TestClass'));

        $this->fixture->setParent($parentMock);

        self::assertSame($parentMock, $this->fixture->getParent());
    }

    public function testSetAndGetParentInterface(): void
    {
        self::assertNull($this->fixture->getParent());

        $parentMock = new InterfaceDescriptor();
        $parentMock->setFullyQualifiedStructuralElementName(new Fqsen('\TestInterface'));
        $this->fixture->setParent($parentMock);

        self::assertSame($parentMock, $this->fixture->getParent());
    }

    public function testSetAndGetTypes(): void
    {
        self::assertEquals(null, $this->fixture->getType());
        $expected = new Array_();

        $this->fixture->setType($expected);

        self::assertSame($expected, $this->fixture->getType());
    }

    public function testDeterminingTypeDerivedFromVarTag(): void
    {
        $expected = new String_();

        $varTag = new VarDescriptor('var');
        $varTag->setType($expected);

        $this->fixture->getTags()->set('var', new Collection([$varTag]));

        self::assertEquals($expected, $this->fixture->getType());
    }

    public function testEmptyCollectionIsReturnedWhenNoVarTagsPresent(): void
    {
        self::assertEquals(new Collection(), $this->fixture->getVar());
    }

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

    public function testSetAndGetValue(): void
    {
        $expression = new Expression('a');
        $this->fixture->setValue($expression);

        self::assertSame($expression, $this->fixture->getValue());
    }

    public function testRetrieveFileAssociatedWithAGlobalConstant(): void
    {
        // Arrange
        $file = $this->whenFixtureIsDirectlyRelatedToAFile();

        // Act
        $result = $this->fixture->getFile();

        // Assert
        self::assertSame($file, $result);
    }

    public function testRetrieveFileAssociatedWithAClassConstant(): void
    {
        // Arrange
        $file = $this->whenFixtureIsRelatedToAClassWithFile();

        // Act
        $result = $this->fixture->getFile();

        // Assert
        self::assertSame($file, $result);
    }

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
     * @return ClassDescriptor
     */
    protected function createParentClassWithSuperClassAndConstant(Type $type, string $constantName)
    {
        // construct the to-be-inherited constant and its @var tag
        $varTag = $this->prophesize(VarDescriptor::class);
        $varTag->getType()->willReturn($type);

        $parentConstant = new ConstantDescriptor();
        $parentConstant->setTags(new Collection(['var' => new Collection([$varTag->reveal()])]));

        // create SuperClassMock and add a Constant collection with out to-be-inherited constant
        $superClass = new ClassDescriptor();
        $superClass->setConstants(new Collection([$constantName => $parentConstant]));

        // create and set the parent class for our fixture
        $parentClass = new ClassDescriptor();
        $parentClass->setFullyQualifiedStructuralElementName(new Fqsen('\TestClass'));
        $parentClass->setParent($superClass);

        return $parentClass;
    }

    /**
     * Sets up mocks as such that the fixture has a file.
     *
     * @return FileDescriptor
     */
    protected function whenFixtureIsDirectlyRelatedToAFile()
    {
        $file = new FileDescriptor('path');
        $this->fixture->setFile($file);

        return $file;
    }

    /**
     * Sets up mocks as such that the fixture has a parent class, with a file.
     *
     * @return FileDescriptor
     */
    protected function whenFixtureIsRelatedToAClassWithFile()
    {
        $file = new FileDescriptor('path');
        $parent = new ClassDescriptor();
        $parent->setFile($file);
        $parent->setFullyQualifiedStructuralElementName(new Fqsen('\Class1'));
        $this->fixture->setParent($parent);

        return $file;
    }

    /** @param string $name The name of the current constant. */
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

    public function testSettingAndGettingVisibility(): void
    {
        self::assertEquals('public', $this->fixture->getVisibility());

        $this->fixture->setVisibility(new Visibility(VisibilityModifier::PRIVATE));

        self::assertEquals('private', $this->fixture->getVisibility());
    }
}
