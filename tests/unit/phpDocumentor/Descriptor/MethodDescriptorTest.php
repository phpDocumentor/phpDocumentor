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
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\String_;

use function iterator_to_array;

/**
 * Tests the functionality for the MethodDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\MethodDescriptor
 * @covers ::<private>
 * @covers ::<protected>
 */
final class MethodDescriptorTest extends MockeryTestCase
{
    /** @var MethodDescriptor $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new MethodDescriptor();
        $this->fixture->setName('method');
    }

    /**
     * @covers ::setParent
     * @covers ::getParent
     */
    public function testSettingAndGettingAParent(): void
    {
        $parent = new ClassDescriptor();
        $parent->setFullyQualifiedStructuralElementName(new Fqsen('\My\Class'));

        $this->assertNull($this->fixture->getParent());

        $this->fixture->setParent($parent);

        $this->assertSame($parent, $this->fixture->getParent());
    }

    /**
     * @covers ::setArguments
     * @covers ::getArguments
     */
    public function testSettingAndGettingArguments(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getArguments());
        $this->assertCount(0, iterator_to_array($this->fixture->getArguments()));

        $argument = new ArgumentDescriptor();
        $argument->setName('name');
        $collection = new Collection([$argument]);

        $this->fixture->setArguments($collection);

        $this->assertInstanceOf(Collection::class, $this->fixture->getArguments());
        $argumentsAsArray = iterator_to_array($this->fixture->getArguments());
        $this->assertCount(1, $argumentsAsArray);
        $this->assertSame($argument, $argumentsAsArray['name']);
        $this->assertSame($argument->getMethod(), $this->fixture);
    }

    /**
     * @covers ::addArgument
     */
    public function testAddingAnArgument(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getArguments());
        $this->assertCount(0, iterator_to_array($this->fixture->getArguments()));

        $argument = new ArgumentDescriptor();
        $argument->setName('name');

        $this->fixture->addArgument('name', $argument);

        $this->assertInstanceOf(Collection::class, $this->fixture->getArguments());
        $argumentsAsArray = iterator_to_array($this->fixture->getArguments());
        $this->assertCount(1, $argumentsAsArray);
        $this->assertSame($argument, $argumentsAsArray['name']);
        $this->assertSame($argument->getMethod(), $this->fixture);
    }

    /**
     * @covers ::isAbstract
     * @covers ::setAbstract
     */
    public function testSettingAndGettingWhetherMethodIsAbstract(): void
    {
        $this->assertFalse($this->fixture->isAbstract());

        $this->fixture->setAbstract(true);

        $this->assertTrue($this->fixture->isAbstract());
    }

    /**
     * @covers ::isFinal
     * @covers ::setFinal
     */
    public function testSettingAndGettingWhetherMethodIsFinal(): void
    {
        $this->assertFalse($this->fixture->isFinal());

        $this->fixture->setFinal(true);

        $this->assertTrue($this->fixture->isFinal());
    }

    /**
     * @covers ::isStatic
     * @covers ::setStatic
     */
    public function testSettingAndGettingWhetherMethodIsStatic(): void
    {
        $this->assertFalse($this->fixture->isStatic());

        $this->fixture->setStatic(true);

        $this->assertTrue($this->fixture->isStatic());
    }

    /**
     * @covers ::getVisibility
     * @covers ::setVisibility
     */
    public function testSettingAndGettingVisibility(): void
    {
        $this->assertEquals('public', $this->fixture->getVisibility());

        $this->fixture->setVisibility('private');

        $this->assertEquals('private', $this->fixture->getVisibility());
    }

    /**
     * @covers ::getResponse
     */
    public function testRetrieveReturnTagForResponse(): void
    {
        $returnDescriptor = new ReturnDescriptor('return');
        $returnDescriptor->setType(new String_());

        $this->assertNull($this->fixture->getResponse()->getType());

        $this->fixture->getTags()->set('return', new Collection([$returnDescriptor]));

        $this->assertSame($returnDescriptor, $this->fixture->getResponse());
    }

    /**
     * @covers ::setReturnType
     * @covers ::getResponse
     */
    public function testGetResponseReturnsReturnType(): void
    {
        $returnType = new String_();
        $this->fixture->setReturnType($returnType);

        $this->assertSame($returnType, $this->fixture->getResponse()->getType());
    }

    /**
     * @covers ::getColumn
     * @covers ::setColumn
     */
    public function testSettingAndGettingColumn(): void
    {
        $this->assertSame(0, $this->fixture->getColumn());

        $this->fixture->setColumn(15);

        $this->assertSame(15, $this->fixture->getColumn());
    }

    /**
     * @covers ::getEndLine
     * @covers ::setEndLine
     */
    public function testSettingAndGettingEndLine(): void
    {
        $this->assertSame(0, $this->fixture->getEndLine());

        $this->fixture->setEndLine(12);

        $this->assertSame(12, $this->fixture->getEndLine());
    }

    /**
     * @covers ::getEndColumn
     * @covers ::setEndColumn
     */
    public function testSettingAndGettingEndColumn(): void
    {
        $this->assertSame(0, $this->fixture->getEndColumn());

        $this->fixture->setEndColumn(230);

        $this->assertSame(230, $this->fixture->getEndColumn());
    }

    /**
     * @covers ::getFile
     */
    public function testRetrieveFileAssociatedWithAMethod(): void
    {
        // Arrange
        $file = $this->whenFixtureIsRelatedToAClassWithFile();

        // Act
        $result = $this->fixture->getFile();

        // Assert
        $this->assertSame($file, $result);
    }

    /**
     * @covers ::getReturn
     */
    public function testReturnTagsInheritWhenNoneArePresent(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getReturn());
        $this->assertSame(0, $this->fixture->getReturn()->count());

        $returnTagDescriptor = new ReturnDescriptor('return');
        $returnCollection = new Collection([$returnTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('return', $returnCollection);

        $result = $this->fixture->getReturn();

        $this->assertEquals($returnCollection, $result);
    }

    /**
     * @covers ::getParam
     */
    public function testParamTagsInheritWhenNoneArePresent(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getParam());
        $this->assertSame(0, $this->fixture->getParam()->count());

        $paramTagDescriptor = new AuthorDescriptor('param');
        $paramCollection = new Collection([$paramTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('param', $paramCollection);

        $result = $this->fixture->getParam();

        $this->assertSame($paramCollection, $result);
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
        $parentProperty = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('author', $authorCollection);

        // Act
        $result = $this->fixture->getAuthor();

        // Assert
        $this->assertSame($authorCollection, $result);
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
        $parentProperty = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('version', $versionCollection);

        // Act
        $result = $this->fixture->getVersion();

        // Assert
        $this->assertSame($versionCollection, $result);
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
        $parentProperty = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('copyright', $copyrightCollection);

        // Act
        $result = $this->fixture->getCopyright();

        // Assert
        $this->assertSame($copyrightCollection, $result);
    }

    /**
     * Sets up mocks as such that the fixture has a parent class, with a file.
     *
     * @return m\MockInterface|FileDescriptor
     */
    private function whenFixtureIsRelatedToAClassWithFile()
    {
        $file = m::mock(FileDescriptor::class);
        $parent = m::mock(ClassDescriptor::class);
        $parent->shouldReceive('getFile')->andReturn($file);
        $parent->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn(new Fqsen('\My\Class1'));
        $this->fixture->setParent($parent);

        return $file;
    }

    /**
     * @param string $name The name of the current method.
     */
    private function whenFixtureHasMethodInParentClassWithSameName(string $name): MethodDescriptor
    {
        $result = new MethodDescriptor();
        $result->setName($name);

        $parent = new ClassDescriptor();
        $parent->setFullyQualifiedStructuralElementName(new Fqsen('\My\Super\Class'));
        $parent->getMethods()->set($name, $result);

        $class = new ClassDescriptor();
        $class->setFullyQualifiedStructuralElementName(new Fqsen('\My\Class'));
        $class->setParent($parent);

        $this->fixture->setParent($class);

        return $result;
    }
}
