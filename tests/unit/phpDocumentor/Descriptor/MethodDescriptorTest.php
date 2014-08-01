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
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;

/**
 * Tests the functionality for the MethodDescriptor class.
 */
class MethodDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var MethodDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new MethodDescriptor();
        $this->fixture->setName('method');
    }

    /**
     * Tests whether all collection objects are properly initialized.
     *
     * @covers phpDocumentor\Descriptor\MethodDescriptor::__construct
     */
    public function testInitialize()
    {
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'arguments', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::setArguments
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getArguments
     */
    public function testSettingAndGettingArguments()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getArguments());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setArguments($mock);

        $this->assertSame($mock, $this->fixture->getArguments());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::isAbstract
     * @covers phpDocumentor\Descriptor\MethodDescriptor::setAbstract
     */
    public function testSettingAndGettingWhetherMethodIsAbstract()
    {
        $this->assertFalse($this->fixture->isAbstract());

        $this->fixture->setAbstract(true);

        $this->assertTrue($this->fixture->isAbstract());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::isFinal
     * @covers phpDocumentor\Descriptor\MethodDescriptor::setFinal
     */
    public function testSettingAndGettingWhetherMethodIsFinal()
    {
        $this->assertFalse($this->fixture->isFinal());

        $this->fixture->setFinal(true);

        $this->assertTrue($this->fixture->isFinal());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::isStatic
     * @covers phpDocumentor\Descriptor\MethodDescriptor::setStatic
     */
    public function testSettingAndGettingWhetherMethodIsStatic()
    {
        $this->assertFalse($this->fixture->isStatic());

        $this->fixture->setStatic(true);

        $this->assertTrue($this->fixture->isStatic());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getVisibility
     * @covers phpDocumentor\Descriptor\MethodDescriptor::setVisibility
     */
    public function testSettingAndGettingVisibility()
    {
        $this->assertEquals('public', $this->fixture->getVisibility());

        $this->fixture->setVisibility('private');

        $this->assertEquals('private', $this->fixture->getVisibility());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getResponse
     */
    public function testRetrieveReturnTagForResponse()
    {
        $mock = new \stdClass();

        $this->assertNull($this->fixture->getResponse());

        $this->fixture->getTags()->set('return', new Collection(array($mock)));

        $this->assertSame($mock, $this->fixture->getResponse());
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getFile
     */
    public function testRetrieveFileAssociatedWithAMethod()
    {
        // Arrange
        $file = $this->whenFixtureIsRelatedToAClassWithFile();

        // Act
        $result = $this->fixture->getFile();

        // Assert
        $this->assertAttributeSame(null, 'fileDescriptor', $this->fixture);
        $this->assertSame($file, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getSummary
     */
    public function testSummaryInheritsWhenNoneIsPresent()
    {
        // Arrange
        $summary = 'This is a summary';
        $this->fixture->setSummary(null);
        $parentMethod = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentMethod->setSummary($summary);

        // Act
        $result = $this->fixture->getSummary();

        // Assert
        $this->assertSame($summary, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getSummary
     */
    public function testSummaryInheritsFromImplementedInterfaceWhenNoneIsPresent()
    {
        // Arrange
        $summary = 'This is a summary';
        $this->fixture->setSummary(null);
        $parentMethod = $this->whenFixtureHasMethodInImplementedInterfaceWithSameName($this->fixture->getName());
        $parentMethod->setSummary($summary);

        // Act
        $result = $this->fixture->getSummary();

        // Assert
        $this->assertSame($summary, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getDescription
     */
    public function testDescriptionInheritsWhenNoneIsPresent()
    {
        // Arrange
        $description = 'This is a description';
        $this->fixture->setDescription(null);
        $parentMethod = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentMethod->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame($description, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getDescription
     */
    public function testDescriptionInheritsWhenInheritDocIsPresent()
    {
        // Arrange
        $description = 'This is a description';
        $this->fixture->setDescription('{@inheritDoc}');
        $parentMethod = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentMethod->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame($description, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getDescription
     */
    public function testDescriptionIsAugmentedWhenInheritDocInlineTagIsPresent()
    {
        // Arrange
        $description = 'This is a description';
        $this->fixture->setDescription('Original description {@inheritDoc}');
        $parentMethod = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentMethod->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame('Original description ' . $description, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getReturn
     */
    public function testReturnTagsInheritWhenNoneArePresent()
    {
        // Arrange
        $returnTagDescriptor = new AuthorDescriptor('return');
        $returnCollection = new Collection(array($returnTagDescriptor));
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('return', $returnCollection);

        // Act
        $result = $this->fixture->getReturn();

        // Assert
        $this->assertSame($returnCollection, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getParam
     */
    public function testParamTagsInheritWhenNoneArePresent()
    {
        // Arrange
        $paramTagDescriptor = new AuthorDescriptor('param');
        $paramCollection = new Collection(array($paramTagDescriptor));
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('param', $paramCollection);

        // Act
        $result = $this->fixture->getParam();

        // Assert
        $this->assertSame($paramCollection, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getAuthor
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getAuthor
     */
    public function testAuthorTagsInheritWhenNoneArePresent()
    {
        // Arrange
        $authorTagDescriptor = new AuthorDescriptor('author');
        $authorCollection = new Collection(array($authorTagDescriptor));
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('author', $authorCollection);

        // Act
        $result = $this->fixture->getAuthor();

        // Assert
        $this->assertSame($authorCollection, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getVersion
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getVersion
     */
    public function testVersionTagsInheritWhenNoneArePresent()
    {
        // Arrange
        $versionTagDescriptor = new VersionDescriptor('version');
        $versionCollection = new Collection(array($versionTagDescriptor));
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('version', $versionCollection);

        // Act
        $result = $this->fixture->getVersion();

        // Assert
        $this->assertSame($versionCollection, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\MethodDescriptor::getCopyright
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getCopyright
     */
    public function testCopyrightTagsInheritWhenNoneArePresent()
    {
        // Arrange
        $copyrightTagDescriptor = new TagDescriptor('copyright');
        $copyrightCollection = new Collection(array($copyrightTagDescriptor));
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasMethodInParentClassWithSameName($this->fixture->getName());
        $parentProperty->getTags()->set('copyright', $copyrightCollection);

        // Act
        $result = $this->fixture->getCopyright();

        // Assert
        $this->assertSame($copyrightCollection, $result);
    }

    /**
     * Sets up mocks as such that the fixture has a file.
     *
     * @return m\MockInterface|FileDescriptor
     */
    protected function whenFixtureIsDirectlyRelatedToAFile()
    {
        $file = m::mock('phpDocumentor\Descriptor\FileDescriptor');
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
        $file = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $parent = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $parent->shouldReceive('getFile')->andReturn($file);
        $parent->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('Class1');
        $this->fixture->setParent($parent);

        return $file;
    }

    /**
     * @param string $name The name of the current method.
     *
     * @return MethodDescriptor
     */
    protected function whenFixtureHasMethodInParentClassWithSameName($name)
    {
        $result = new MethodDescriptor;
        $result->setName($name);

        $parent = new ClassDescriptor();
        $parent->getMethods()->set($name, $result);

        $class  = new ClassDescriptor();
        $class->setParent($parent);

        $this->fixture->setParent($class);

        return $result;
    }

    /**
     * @param string $name The name of the current method.
     *
     * @return MethodDescriptor
     */
    protected function whenFixtureHasMethodInImplementedInterfaceWithSameName($name)
    {
        $result = new MethodDescriptor;
        $result->setName($name);

        $parent = new InterfaceDescriptor();
        $parent->getMethods()->set($name, $result);

        $class  = new ClassDescriptor();
        $class->getInterfaces()->set('Implemented', $parent);

        $this->fixture->setParent($class);

        return $result;
    }
}
