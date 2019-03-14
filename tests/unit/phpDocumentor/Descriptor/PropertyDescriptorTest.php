<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use \Mockery as m;
use \phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use \phpDocumentor\Descriptor\Tag\VarDescriptor;
use \phpDocumentor\Descriptor\Tag\VersionDescriptor;
use phpDocumentor\Reflection\Types\Array_;

/**
 * Tests the functionality for the PropertyDescriptor class.
 */
class PropertyDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var PropertyDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new PropertyDescriptor();
        $this->fixture->setName('property');
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::isStatic
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::setStatic
     */
    public function testSettingAndGettingWhetherPropertyIsStatic()
    {
        $this->assertFalse($this->fixture->isStatic());

        $this->fixture->setStatic(true);

        $this->assertTrue($this->fixture->isStatic());
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getVisibility
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::setVisibility
     */
    public function testSettingAndGettingVisibility()
    {
        $this->assertEquals('public', $this->fixture->getVisibility());

        $this->fixture->setVisibility('private');

        $this->assertEquals('private', $this->fixture->getVisibility());
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getType
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::setType
     */
    public function testSetAndGetTypes()
    {
        $this->assertEquals(null, $this->fixture->getType());
        $expected = new Array_();

        $this->fixture->setType($expected);

        $this->assertSame($expected, $this->fixture->getType());
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getType
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::setType
     */
    public function testSetAndGetTypesWhenVarIsPresent()
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
        $this->assertSame($typesCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getDefault
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::setDefault
     */
    public function testSetAndGetDefault()
    {
        $this->assertNull($this->fixture->getDefault());

        $this->fixture->setDefault('a');

        $this->assertSame('a', $this->fixture->getDefault());
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getFile
     */
    public function testRetrieveFileAssociatedWithAProperty()
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
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getSummary
     */
    public function testSummaryInheritsWhenNoneIsPresent()
    {
        // Arrange
        $summary = 'This is a summary';
        $this->fixture->setSummary(null);
        $parentProperty = $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        $parentProperty->setSummary($summary);

        // Act
        $result = $this->fixture->getSummary();

        // Assert
        $this->assertSame($summary, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getDescription
     */
    public function testDescriptionInheritsWhenNoneIsPresent()
    {
        // Arrange
        $description = 'This is a description';
        $this->fixture->setDescription(null);
        $parentProperty = $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        $parentProperty->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame($description, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getDescription
     */
    public function testDescriptionInheritsWhenInheritDocIsPresent()
    {
        // Arrange
        $description = 'This is a description';
        $this->fixture->setDescription('{@inheritDoc}');
        $parentProperty = $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        $parentProperty->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame($description, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getDescription
     */
    public function testDescriptionIsAugmentedWhenInheritDocInlineTagIsPresent()
    {
        // Arrange
        $description = 'This is a description';
        $this->fixture->setDescription('Original description {@inheritDoc}');
        $parentProperty = $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        $parentProperty->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame('Original description ' . $description, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getVar
     */
    public function testVarTagsInheritWhenNoneArePresent()
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
        $this->assertSame($varCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getVar
     */
    public function testVarTagsWhenNoneArePresent()
    {
        $varCollection = new Collection();
        $result = $this->fixture->getVar();

        $this->assertEquals($varCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getAuthor
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getAuthor
     */
    public function testAuthorTagsInheritWhenNoneArePresent()
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
        $this->assertSame($authorCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getVersion
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getVersion
     */
    public function testVersionTagsInheritWhenNoneArePresent()
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
        $this->assertSame($versionCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getCopyright
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getCopyright
     */
    public function testCopyrightTagsInheritWhenNoneArePresent()
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
        $this->assertSame($copyrightCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::setParent
     */
    public function testFqsenHasDollarSignWhenParentIsSet()
    {
        $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        $this->assertSame('::$property', $this->fixture->getFullyQualifiedStructuralElementName());
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::setParent
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getParent
     */
    public function testSettingAndGettingAParent()
    {
        $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());
        $this->assertInstanceOf('\phpDocumentor\Descriptor\ClassDescriptor', $this->fixture->getParent());
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getInheritedElement
     */
    public function testGettingAnInheritedElement()
    {
        $this->whenFixtureHasPropertyInParentClassWithSameName($this->fixture->getName());

        $inheritedProperty = $this->fixture->getInheritedElement();

        $this->assertSame($inheritedProperty->getName(), $this->fixture->getName());
    }

    /**
     * @covers \phpDocumentor\Descriptor\PropertyDescriptor::getInheritedElement
     */
    public function testGettingAnInheritedElementWhenThereIsNone()
    {
        $this->assertNull($this->fixture->getInheritedElement());
    }

    /**
     * Sets up mocks as such that the fixture has a file.
     *
     * @return m\MockInterface|FileDescriptor
     */
    protected function whenFixtureIsDirectlyRelatedToAFile()
    {
        $file = m::mock('\phpDocumentor\Descriptor\FileDescriptor');
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
        $file = m::mock('\phpDocumentor\Descriptor\FileDescriptor');
        $parent = m::mock('\phpDocumentor\Descriptor\ClassDescriptor');
        $parent->shouldReceive('getFile')->andReturn($file);
        $parent->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('Class1');
        $this->fixture->setParent($parent);

        return $file;
    }

    /**
     * @param string $name The name of the current property.
     *
     * @return PropertyDescriptor
     */
    protected function whenFixtureHasPropertyInParentClassWithSameName($name)
    {
        $result = new PropertyDescriptor();
        $result->setName($name);

        $parent = new ClassDescriptor();
        $parent->getProperties()->set($name, $result);

        $class = new ClassDescriptor();
        $class->setParent($parent);

        $this->fixture->setParent($class);

        return $result;
    }
}
