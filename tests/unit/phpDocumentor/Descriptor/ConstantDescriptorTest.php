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

/**
 * Tests the functionality for the ConstantDescriptor class.
 */
class ConstantDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ConstantDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ConstantDescriptor();
        $this->fixture->setName('CONSTANT');
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getParent
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::setParent
     */
    public function testSetAndGetParentClass()
    {
        $this->assertSame(null, $this->fixture->getParent());

        $parentMock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $parentMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('TestClass');

        $this->fixture->setParent($parentMock);

        $this->assertSame($parentMock, $this->fixture->getParent());
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::setParent
     * @expectedException \InvalidArgumentException
     */
    public function testSettingAParentFailsWhenInputIsNotNullClassOrInterface()
    {
        $this->fixture->setParent('string');
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getParent
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::setParent
     */
    public function testSetAndGetParentInterface()
    {
        $this->assertSame(null, $this->fixture->getParent());

        $parentMock = m::mock('phpDocumentor\Descriptor\InterfaceDescriptor');
        $parentMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('TestInterface');
        $this->fixture->setParent($parentMock);

        $this->assertSame($parentMock, $this->fixture->getParent());
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getTypes
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::setTypes
     */
    public function testSetAndGetTypes()
    {
        $this->assertSame(array(), $this->fixture->getTypes());

        $this->fixture->setTypes(array(1));

        $this->assertSame(array(1), $this->fixture->getTypes());
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getTypes
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getVar
     */
    public function testgetTypesDerivedFromVarTag()
    {
        $expected = array('string', 'null');

        $varTag = m::mock('phpDocumentor\Descriptor\Tag\VarDescriptor');
        $varTag->shouldReceive('getTypes')->andReturn($expected);

        $this->fixture->getTags()->set('var', new Collection(array($varTag)));

        $this->assertSame($expected, $this->fixture->getTypes());
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getTypes
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getVar
     */
    public function testGetTypesUsingInheritanceOfVarTag()
    {
        $expected = array('string', 'null');

        $constantName = 'CONSTANT';
        $this->fixture->setName($constantName);
        $parentClass = $this->createParentClassWithSuperClassAndConstant($expected, $constantName);

        // Attempt to get the types; which come from the superclass' constants
        $this->fixture->setParent($parentClass);
        $types = $this->fixture->getTypes();

        $this->assertSame($expected, $types);
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getValue
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::setValue
     */
    public function testSetAndGetValue()
    {
        $this->assertSame(null, $this->fixture->getValue());

        $this->fixture->setValue('a');

        $this->assertSame('a', $this->fixture->getValue());
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getFile
     */
    public function testRetrieveFileAssociatedWithAGlobalConstant()
    {
        // Arrange
        $file = $this->whenFixtureIsDirectlyRelatedToAFile();

        // Act
        $result = $this->fixture->getFile();

        // Assert
        $this->assertSame($file, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getFile
     */
    public function testRetrieveFileAssociatedWithAClassConstant()
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
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getSummary
     */
    public function testSummaryInheritsWhenNoneIsPresent()
    {
        // Arrange
        $summary = 'This is a summary';
        $this->fixture->setSummary(null);
        $parentConstant = $this->whenFixtureHasConstantInParentClassWithSameName($this->fixture->getName());
        $parentConstant->setSummary($summary);

        // Act
        $result = $this->fixture->getSummary();

        // Assert
        $this->assertSame($summary, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\ConstantDescriptor::getSummary
     */
    public function testDescriptionInheritsWhenNoneIsPresent()
    {
        // Arrange
        $description = 'This is a description';
        $this->fixture->setDescription(null);
        $parentConstant = $this->whenFixtureHasConstantInParentClassWithSameName($this->fixture->getName());
        $parentConstant->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame($description, $result);
    }

    /**
     * Creates a parentClass for a Constant with a SuperClass, which in turn has a constant exposing the given types.
     *
     * The created ParentClass can be used to test the inheritance of properties of a constant descriptor, such as
     * inheriting type information.
     *
     * @param string[] $types
     * @param string $constantName
     *
     * @return m\MockInterface|ClassDescriptor
     */
    protected function createParentClassWithSuperClassAndConstant($types, $constantName)
    {
        // construct the to-be-inherited constant and its @var tag
        $varTag = m::mock('phpDocumentor\Descriptor\Tag\VarDescriptor');
        $varTag->shouldReceive('getTypes')->andReturn($types);

        $parentConstant = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');
        $parentConstant->shouldReceive('getVar')->andReturn(new Collection(array($varTag)));

        // create SuperClassMock and add a Constant collection with out to-be-inherited constant
        $superClass = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $superClass->shouldReceive('getConstants')->andReturn(
            new Collection(
                array($constantName => $parentConstant)
            )
        );

        // create and set the parent class for our fixture
        $parentClass = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $parentClass->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('TestClass');
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
     * @param string $name The name of the current constant.
     *
     * @return ConstantDescriptor
     */
    protected function whenFixtureHasConstantInParentClassWithSameName($name)
    {
        $result = new ConstantDescriptor;
        $result->setName($name);

        $parent = new ClassDescriptor();
        $parent->getConstants()->set($name, $result);

        $class  = new ClassDescriptor();
        $class->setParent($parent);

        $this->fixture->setParent($class);

        return $result;
    }
}
