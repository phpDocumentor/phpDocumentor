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

use Mockery as m;
use phpDocumentor\Reflection\Types\String_;

/**
 * Tests the functionality for the ClassDescriptor class.
 */
class ClassDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ClassDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ClassDescriptor();
    }

    /**
     * Tests whether all collection objects are properly initialized.
     *
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::__construct
     */
    public function testInitialize()
    {
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'implements', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'constants', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'properties', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'methods', $this->fixture);
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setParent
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getParent
     */
    public function testSettingAndGettingAParent()
    {
        $this->assertNull($this->fixture->getParent());

        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');

        $this->fixture->setParent($mock);

        $this->assertSame($mock, $this->fixture->getParent());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setParent
     */
    public function testSettingNoParent()
    {
        $mock = null;

        $this->fixture->setParent($mock);

        $this->assertSame($mock, $this->fixture->getParent());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setInterfaces
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInterfaces
     */
    public function testSettingAndGettingInterfaces()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getInterfaces());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setInterfaces($mock);

        $this->assertSame($mock, $this->fixture->getInterfaces());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setConstants
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getConstants
     */
    public function testSettingAndGettingConstants()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getConstants());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setConstants($mock);

        $this->assertSame($mock, $this->fixture->getConstants());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setProperties
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getProperties
     */
    public function testSettingAndGettingProperties()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getProperties());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setProperties($mock);

        $this->assertSame($mock, $this->fixture->getProperties());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setMethods
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getMethods
     */
    public function testSettingAndGettingMethods()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMethods());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsEmptyCollectionWithoutParent()
    {
        $inheritedMethods = $this->fixture->getInheritedMethods();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $inheritedMethods);
        $this->assertCount(0, $inheritedMethods);
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsCollectionWithParent()
    {
        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldReceive('getMethods')->andReturn(new Collection(array('methods')));
        $mock->shouldReceive('getInheritedMethods')->andReturn(new Collection(array('inherited')));

        $this->fixture->setParent($mock);
        $result = $this->fixture->getInheritedMethods();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $expected = array('methods', 'inherited');
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsTraitMethods()
    {
        // Arrange
        $expected = array('methods');
        $traitDescriptorMock = m::mock('phpDocumentor\Descriptor\TraitDescriptor');
        $traitDescriptorMock->shouldReceive('getMethods')->andReturn(new Collection(array('methods')));
        $this->fixture->setUsedTraits(new Collection(array($traitDescriptorMock)));

        // Act
        $result = $this->fixture->getInheritedMethods();

        // Assert
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedMethods
     * @ticket https://github.com/phpDocumentor/phpDocumentor2/issues/1307
     */
    public function testRetrievingInheritedMethodsDoesNotCrashWhenUsedTraitIsNotInProject()
    {
        // Arrange
        $expected = array();
        // unknown traits are not converted to TraitDescriptors but kept as strings
        $this->fixture->setUsedTraits(new Collection(array('unknownTrait')));

        // Act
        $result = $this->fixture->getInheritedMethods();

        // Assert
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::isAbstract
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setAbstract
     */
    public function testSettingAndGettingWhetherClassIsAbstract()
    {
        $this->assertFalse($this->fixture->isAbstract());

        $this->fixture->setAbstract(true);

        $this->assertTrue($this->fixture->isAbstract());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::isFinal
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setFinal
     */
    public function testSettingAndGettingWhetherClassIsFinal()
    {
        $this->assertFalse($this->fixture->isFinal());

        $this->fixture->setFinal(true);

        $this->assertTrue($this->fixture->isFinal());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getMagicProperties
     */
    public function testGetMagicPropertiesUsingPropertyTags()
    {
        $variableName = 'variableName';
        $description  = 'description';
        $types        = new Collection(array('string'));

        $this->assertEquals(0, $this->fixture->getMagicProperties()->count());

        $propertyMock = m::mock('phpDocumentor\Descriptor\Tag\PropertyDescriptor');
        $propertyMock->shouldReceive('getVariableName')->andReturn($variableName);
        $propertyMock->shouldReceive('getDescription')->andReturn($description);
        $propertyMock->shouldReceive('getTypes')->andReturn(new String_());

        $this->fixture->getTags()->get('property', new Collection())->add($propertyMock);

        $magicProperties = $this->fixture->getMagicProperties();

        $this->assertCount(1, $magicProperties);

        /** @var PropertyDescriptor $magicProperty */
        $magicProperty = current($magicProperties->getAll());
        $this->assertEquals($variableName, $magicProperty->getName());
        $this->assertEquals($description, $magicProperty->getDescription());
        $this->assertEquals(new String_(), $magicProperty->getTypes());

        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldReceive('getMagicProperties')->andReturn(new Collection(array('magicProperties')));
        $this->fixture->setParent($mock);

        $magicProperties = $this->fixture->getMagicProperties();
        $this->assertCount(2, $magicProperties);
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedConstants
     */
    public function testGetInheritedConstantsNoParent()
    {
        $descriptor = new ClassDescriptor();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $descriptor->getInheritedConstants());

        $descriptor->setParent(new \stdClass());
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $descriptor->getInheritedConstants());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedConstants
     */
    public function testGetInheritedConstantsWithClassDescriptorParent()
    {
        $collectionMock = m::mock('phpDocumentor\Descriptor\Collection');
        $collectionMock->shouldReceive('get');
        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldReceive('getConstants')->andReturn(new Collection(array('constants')));
        $mock->shouldReceive('getInheritedConstants')->andReturn(new Collection(array('inherited')));

        $this->fixture->setParent($mock);
        $result = $this->fixture->getInheritedConstants();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $expected = array('constants', 'inherited');
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedProperties
     */
    public function testGetInheritedPropertiesNoParent()
    {
        $descriptor = new ClassDescriptor();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $descriptor->getInheritedProperties());

        $descriptor->setParent(new \stdClass());
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $descriptor->getInheritedProperties());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedProperties
     */
    public function testGetInheritedPropertiesWithClassDescriptorParent()
    {
        $collectionMock = m::mock('phpDocumentor\Descriptor\Collection');
        $collectionMock->shouldReceive('get');
        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldReceive('getProperties')->andReturn(new Collection(array('properties')));
        $mock->shouldReceive('getInheritedProperties')->andReturn(new Collection(array('inherited')));

        $this->fixture->setParent($mock);
        $result = $this->fixture->getInheritedProperties();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $expected = array('properties', 'inherited');
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedProperties
     */
    public function testRetrievingInheritedPropertiesReturnsTraitProperties()
    {
        // Arrange
        $expected = array('properties');
        $traitDescriptorMock = m::mock('phpDocumentor\Descriptor\TraitDescriptor');
        $traitDescriptorMock->shouldReceive('getProperties')->andReturn(new Collection(array('properties')));
        $this->fixture->setUsedTraits(new Collection(array($traitDescriptorMock)));

        // Act
        $result = $this->fixture->getInheritedProperties();

        // Assert
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedProperties
     * @ticket https://github.com/phpDocumentor/phpDocumentor2/issues/1307
     */
    public function testRetrievingInheritedPropertiesDoesNotCrashWhenUsedTraitIsNotInProject()
    {
        // Arrange
        $expected = array();
        // unknown traits are not converted to TraitDescriptors but kept as strings
        $this->fixture->setUsedTraits(new Collection(array('unknownTrait')));

        // Act
        $result = $this->fixture->getInheritedProperties();

        // Assert
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getMagicMethods
     * @dataProvider provideMagicMethodProperties
     * @param bool $isStatic
     */
    public function testGetMagicMethods($isStatic)
    {
        $methodName  = 'methodName';
        $description = 'description';
        $response    = array('string');
        $arguments   = m::mock('phpDocumentor\Descriptor\Tag\ArgumentDescriptor');
        $arguments->shouldReceive('setMethod');

        $this->assertEquals(0, $this->fixture->getMagicMethods()->count());

        $methodMock = m::mock('phpDocumentor\Descriptor\Tag\MethodDescriptor');
        $methodMock->shouldReceive('getMethodName')->andReturn($methodName);
        $methodMock->shouldReceive('getDescription')->andReturn($description);
        $methodMock->shouldReceive('getResponse')->andReturn($response);
        $methodMock->shouldReceive('getArguments')->andReturn($arguments);
        $methodMock->shouldReceive('isStatic')->andReturn($isStatic);

        $this->fixture->getTags()->get('method', new Collection())->add($methodMock);

        $magicMethods = $this->fixture->getMagicMethods();

        $this->assertCount(1, $magicMethods);

        /** @var MethodDescriptor $magicMethod */
        $magicMethod = current($magicMethods->getAll());
        $this->assertEquals($methodName, $magicMethod->getName());
        $this->assertEquals($description, $magicMethod->getDescription());
        $this->assertEquals($response, $magicMethod->getResponse());
        $this->assertEquals($isStatic, $magicMethod->isStatic());

        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldReceive('getMagicMethods')->andReturn(new Collection(array('magicMethods')));
        $this->fixture->setParent($mock);

        $magicMethods = $this->fixture->getMagicMethods();
        $this->assertCount(2, $magicMethods);
    }

    /**
     * Provider to test different properties for a class magic method
     * (provides isStatic)
     * @return bool[][]
     */
    public function provideMagicMethodProperties()
    {
        return array(
            // Instance magic method (default)
            array(false),
            // Static magic method
            array(true),
        );
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setPackage
     */
    public function testSetPackage()
    {
        $package = 'Package';

        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldDeferMissing();

        $constantDescriptor = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');
        $constantCollection = m::mock('phpDocumentor\Descriptor\Collection');
        $constantCollection->shouldDeferMissing();
        $constantCollection->add($constantDescriptor);

        $propertyDescriptor = m::mock('phpDocumentor\Descriptor\PropertyDescriptor');
        $propertyCollection = m::mock('phpDocumentor\Descriptor\Collection');
        $propertyCollection->shouldDeferMissing();
        $propertyCollection->add($propertyDescriptor);

        $methodDescriptor = m::mock('phpDocumentor\Descriptor\MethodDescriptor');
        $methodCollection = m::mock('phpDocumentor\Descriptor\Collection');
        $methodCollection->shouldDeferMissing();
        $methodCollection->add($methodDescriptor);

        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldDeferMissing();
        $mock->shouldReceive('getProperties')->andReturn($propertyCollection);

        $mock->shouldReceive('getConstants')->andReturn($constantCollection);
        $constantDescriptor->shouldReceive('setPackage')->with($package);

        $mock->shouldReceive('getProperties')->andReturn($propertyCollection);
        $propertyDescriptor->shouldReceive('setPackage')->with($package);

        $mock->shouldReceive('getMethods')->andReturn($methodCollection);
        $methodDescriptor->shouldReceive('setPackage')->with($package);

        $mock->setPackage($package);

        $this->assertTrue(true);
    }

    /**
     * Test to cover magic method of parent abstract class
     *
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::__call
     */
    public function testCall()
    {
        $this->assertNull($this->fixture->notexisting());
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getNotexisting());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getSummary
     */
    public function testSummaryInheritsWhenNoneIsPresent()
    {
        // Arrange
        $summary = 'This is a summary';
        $this->fixture->setSummary(null);
        $parentInterface = $this->whenFixtureHasParentClass();
        $parentInterface->setSummary($summary);

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
        $parentInterface = $this->whenFixtureHasParentClass();
        $parentInterface->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame($description, $result);
    }

    /**
     * @return ClassDescriptor
     */
    protected function whenFixtureHasParentClass()
    {
        $class = new ClassDescriptor();
        $this->fixture->setParent($class);

        return $class;
    }
}
