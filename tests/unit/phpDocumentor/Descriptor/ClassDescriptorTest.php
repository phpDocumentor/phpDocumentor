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

use Mockery as m;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\Types\String_;

/**
 * Tests the functionality for the ClassDescriptor class.
 */
class ClassDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ClassDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new ClassDescriptor();
    }

    /**
     * Tests whether all collection objects are properly initialized.
     *
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::__construct
     */
    public function testInitialize() : void
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
    public function testSettingAndGettingAParent() : void
    {
        $this->assertNull($this->fixture->getParent());

        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');

        $this->fixture->setParent($mock);

        $this->assertSame($mock, $this->fixture->getParent());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setParent
     */
    public function testSettingNoParent() : void
    {
        $mock = null;

        $this->fixture->setParent($mock);

        $this->assertSame($mock, $this->fixture->getParent());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setInterfaces
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInterfaces
     */
    public function testSettingAndGettingInterfaces() : void
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
    public function testSettingAndGettingConstants() : void
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
    public function testSettingAndGettingProperties() : void
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
    public function testSettingAndGettingMethods() : void
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMethods());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsEmptyCollectionWithoutParent() : void
    {
        $inheritedMethods = $this->fixture->getInheritedMethods();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $inheritedMethods);
        $this->assertCount(0, $inheritedMethods);
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsCollectionWithParent() : void
    {
        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldReceive('getMethods')->andReturn(new Collection(['methods']));
        $mock->shouldReceive('getInheritedMethods')->andReturn(new Collection(['inherited']));

        $this->fixture->setParent($mock);
        $result = $this->fixture->getInheritedMethods();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $expected = ['methods', 'inherited'];
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsTraitMethods() : void
    {
        // Arrange
        $expected = ['methods'];
        $traitDescriptorMock = m::mock('phpDocumentor\Descriptor\TraitDescriptor');
        $traitDescriptorMock->shouldReceive('getMethods')->andReturn(new Collection(['methods']));
        $this->fixture->setUsedTraits(new Collection([$traitDescriptorMock]));

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
    public function testRetrievingInheritedMethodsDoesNotCrashWhenUsedTraitIsNotInProject() : void
    {
        // Arrange
        $expected = [];
        // unknown traits are not converted to TraitDescriptors but kept as strings
        $this->fixture->setUsedTraits(new Collection(['unknownTrait']));

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
    public function testSettingAndGettingWhetherClassIsAbstract() : void
    {
        $this->assertFalse($this->fixture->isAbstract());

        $this->fixture->setAbstract(true);

        $this->assertTrue($this->fixture->isAbstract());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::isFinal
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setFinal
     */
    public function testSettingAndGettingWhetherClassIsFinal() : void
    {
        $this->assertFalse($this->fixture->isFinal());

        $this->fixture->setFinal(true);

        $this->assertTrue($this->fixture->isFinal());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getMagicProperties
     */
    public function testGetMagicPropertiesUsingPropertyTags() : void
    {
        $variableName = 'variableName';
        $description = 'description';
        $types = new Collection(['string']);

        $this->assertEquals(0, $this->fixture->getMagicProperties()->count());

        $propertyMock = m::mock('phpDocumentor\Descriptor\Tag\PropertyDescriptor');
        $propertyMock->shouldReceive('getVariableName')->andReturn($variableName);
        $propertyMock->shouldReceive('getDescription')->andReturn($description);
        $propertyMock->shouldReceive('getType')->andReturn(new String_());

        $this->fixture->getTags()->get('property', new Collection())->add($propertyMock);

        $magicProperties = $this->fixture->getMagicProperties();

        $this->assertCount(1, $magicProperties);

        /** @var PropertyDescriptor $magicProperty */
        $magicProperty = current($magicProperties->getAll());
        $this->assertEquals($variableName, $magicProperty->getName());
        $this->assertEquals($description, $magicProperty->getDescription());
        $this->assertEquals([new String_()], $magicProperty->getTypes());

        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldReceive('getMagicProperties')->andReturn(new Collection(['magicProperties']));
        $this->fixture->setParent($mock);

        $magicProperties = $this->fixture->getMagicProperties();
        $this->assertCount(2, $magicProperties);
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedConstants
     */
    public function testGetInheritedConstantsNoParent() : void
    {
        $descriptor = new ClassDescriptor();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $descriptor->getInheritedConstants());

        $descriptor->setParent(new \stdClass());
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $descriptor->getInheritedConstants());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedConstants
     */
    public function testGetInheritedConstantsWithClassDescriptorParent() : void
    {
        $collectionMock = m::mock('phpDocumentor\Descriptor\Collection');
        $collectionMock->shouldReceive('get');
        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldReceive('getConstants')->andReturn(new Collection(['constants']));
        $mock->shouldReceive('getInheritedConstants')->andReturn(new Collection(['inherited']));

        $this->fixture->setParent($mock);
        $result = $this->fixture->getInheritedConstants();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $expected = ['constants', 'inherited'];
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedProperties
     */
    public function testGetInheritedPropertiesNoParent() : void
    {
        $descriptor = new ClassDescriptor();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $descriptor->getInheritedProperties());

        $descriptor->setParent(new \stdClass());
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $descriptor->getInheritedProperties());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedProperties
     */
    public function testGetInheritedPropertiesWithClassDescriptorParent() : void
    {
        $collectionMock = m::mock('phpDocumentor\Descriptor\Collection');
        $collectionMock->shouldReceive('get');
        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldReceive('getProperties')->andReturn(new Collection(['properties']));
        $mock->shouldReceive('getInheritedProperties')->andReturn(new Collection(['inherited']));

        $this->fixture->setParent($mock);
        $result = $this->fixture->getInheritedProperties();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $expected = ['properties', 'inherited'];
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::getInheritedProperties
     */
    public function testRetrievingInheritedPropertiesReturnsTraitProperties() : void
    {
        // Arrange
        $expected = ['properties'];
        $traitDescriptorMock = m::mock('phpDocumentor\Descriptor\TraitDescriptor');
        $traitDescriptorMock->shouldReceive('getProperties')->andReturn(new Collection(['properties']));
        $this->fixture->setUsedTraits(new Collection([$traitDescriptorMock]));

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
    public function testRetrievingInheritedPropertiesDoesNotCrashWhenUsedTraitIsNotInProject() : void
    {
        // Arrange
        $expected = [];
        // unknown traits are not converted to TraitDescriptors but kept as strings
        $this->fixture->setUsedTraits(new Collection(['unknownTrait']));

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
    public function testGetMagicMethods($isStatic) : void
    {
        $methodName = 'methodName';
        $description = 'description';
        $response = new ReturnDescriptor('return');
        $response->setType(new String_());
        $arguments = m::mock('phpDocumentor\Descriptor\Tag\ArgumentDescriptor');
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
        $mock->shouldReceive('getMagicMethods')->andReturn(new Collection(['magicMethods']));
        $this->fixture->setParent($mock);

        $magicMethods = $this->fixture->getMagicMethods();
        $this->assertCount(2, $magicMethods);
    }

    /**
     * Provider to test different properties for a class magic method
     * (provides isStatic)
     * @return bool[][]
     */
    public function provideMagicMethodProperties() : array
    {
        return [
            // Instance magic method (default)
            [false],
            // Static magic method
            [true],
        ];
    }

    /**
     * @covers \phpDocumentor\Descriptor\ClassDescriptor::setPackage
     */
    public function testSetPackage() : void
    {
        $package = 'Package';

        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldDeferMissing();

        $constantDescriptor = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');

        /** @var m\mockInterface|Collection */
        $constantCollection = m::mock('phpDocumentor\Descriptor\Collection');
        $constantCollection->shouldDeferMissing();
        $constantCollection->add($constantDescriptor);

        $propertyDescriptor = m::mock('phpDocumentor\Descriptor\PropertyDescriptor');

        /** @var m\mockInterface|Collection */
        $propertyCollection = m::mock('phpDocumentor\Descriptor\Collection');
        $propertyCollection->shouldDeferMissing();
        $propertyCollection->add($propertyDescriptor);

        $methodDescriptor = m::mock('phpDocumentor\Descriptor\MethodDescriptor');

        /** @var m\mockInterface|Collection */
        $methodCollection = m::mock('phpDocumentor\Descriptor\Collection');
        $methodCollection->shouldDeferMissing();
        $methodCollection->add($methodDescriptor);

        /** @var m\mockInterface|ClassDescriptor */
        $mock = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $mock->shouldDeferMissing();
        $mock->shouldReceive('getProperties')->andReturn($propertyCollection);

        $mock->shouldReceive('getConstants')->andReturn($constantCollection);
        $constantDescriptor->shouldReceive('setPackage')->with($package);

        $mock->shouldReceive('getProperties')->andReturn($propertyCollection);
        $propertyDescriptor->shouldReceive('setPackage')->with($package);

        $mock->shouldReceive('getMethods')->andReturn($methodCollection);
        $methodDescriptor->shouldReceive('setPackage')->with($package);

        /** @var ClassDescriptor */
        $mock->setPackage($package);

        $this->assertTrue(true);
    }

    /**
     * Test to cover magic method of parent abstract class
     *
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::__call
     */
    public function testCall() : void
    {
        $this->assertNull($this->fixture->__call('notexisting', []));
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->__call('getNotexisting', []));
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getSummary
     */
    public function testSummaryInheritsWhenNoneIsPresent() : void
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
    public function testDescriptionInheritsWhenNoneIsPresent() : void
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
    protected function whenFixtureHasParentClass() : ClassDescriptor
    {
        $class = new ClassDescriptor();
        $this->fixture->setParent($class);

        return $class;
    }
}
