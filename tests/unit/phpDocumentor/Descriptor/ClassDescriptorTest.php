<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\Tag\MethodDescriptor as TagMethodDescriptor;
use phpDocumentor\Descriptor\Tag\PropertyDescriptor as TagPropertyDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\String_;
use stdClass;
use function current;

/**
 * Tests the functionality for the ClassDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\ClassDescriptor
 */
final class ClassDescriptorTest extends MockeryTestCase
{
    /** @var ClassDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new ClassDescriptor();
        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\My\Class'));
    }

    /**
     * @covers ::setParent
     * @covers ::getParent
     */
    public function testSettingAndGettingAParent() : void
    {
        $this->assertNull($this->fixture->getParent());

        $mock = m::mock(ClassDescriptor::class);

        $this->fixture->setParent($mock);

        $this->assertSame($mock, $this->fixture->getParent());
    }

    /**
     * @covers ::setParent
     */
    public function testSettingNoParent() : void
    {
        $mock = null;

        $this->fixture->setParent($mock);

        $this->assertSame($mock, $this->fixture->getParent());
    }

    /**
     * @covers ::setInterfaces
     * @covers ::getInterfaces
     */
    public function testSettingAndGettingInterfaces() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getInterfaces());

        $mock = m::mock(Collection::class);

        $this->fixture->setInterfaces($mock);

        $this->assertSame($mock, $this->fixture->getInterfaces());
    }

    /**
     * @covers ::setConstants
     * @covers ::getConstants
     */
    public function testSettingAndGettingConstants() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getConstants());

        $mock = m::mock(Collection::class);

        $this->fixture->setConstants($mock);

        $this->assertSame($mock, $this->fixture->getConstants());
    }

    /**
     * @covers ::setProperties
     * @covers ::getProperties
     */
    public function testSettingAndGettingProperties() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getProperties());

        $mock = m::mock(Collection::class);

        $this->fixture->setProperties($mock);

        $this->assertSame($mock, $this->fixture->getProperties());
    }

    /**
     * @covers ::setMethods
     * @covers ::getMethods
     */
    public function testSettingAndGettingMethods() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMethods());

        $mock = m::mock(Collection::class);

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    /**
     * @covers ::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsEmptyCollectionWithoutParent() : void
    {
        $inheritedMethods = $this->fixture->getInheritedMethods();
        $this->assertInstanceOf(Collection::class, $inheritedMethods);
        $this->assertCount(0, $inheritedMethods);
    }

    /**
     * @covers ::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsCollectionWithParent() : void
    {
        $mock = m::mock(ClassDescriptor::class);
        $mock->shouldReceive('getMethods')->andReturn(new Collection(['methods']));
        $mock->shouldReceive('getInheritedMethods')->andReturn(new Collection(['inherited']));

        $this->fixture->setParent($mock);
        $result = $this->fixture->getInheritedMethods();

        $this->assertInstanceOf(Collection::class, $result);

        $expected = ['methods', 'inherited'];
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers ::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsTraitMethods() : void
    {
        // Arrange
        $expected = ['methods'];
        $traitDescriptorMock = m::mock(TraitDescriptor::class);
        $traitDescriptorMock->shouldReceive('getMethods')->andReturn(new Collection(['methods']));
        $this->fixture->setUsedTraits(new Collection([$traitDescriptorMock]));

        // Act
        $result = $this->fixture->getInheritedMethods();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers ::getInheritedMethods
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
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers ::isAbstract
     * @covers ::setAbstract
     */
    public function testSettingAndGettingWhetherClassIsAbstract() : void
    {
        $this->assertFalse($this->fixture->isAbstract());

        $this->fixture->setAbstract(true);

        $this->assertTrue($this->fixture->isAbstract());
    }

    /**
     * @covers ::isFinal
     * @covers ::setFinal
     */
    public function testSettingAndGettingWhetherClassIsFinal() : void
    {
        $this->assertFalse($this->fixture->isFinal());

        $this->fixture->setFinal(true);

        $this->assertTrue($this->fixture->isFinal());
    }

    /**
     * @covers ::getMagicProperties
     */
    public function testGetMagicPropertiesUsingPropertyTags() : void
    {
        $variableName = 'variableName';
        $description = 'description';
        $types = new Collection(['string']);

        $this->assertEquals(0, $this->fixture->getMagicProperties()->count());

        $propertyMock = m::mock(TagPropertyDescriptor::class);
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

        $mock = m::mock(ClassDescriptor::class);
        $mock->shouldReceive('getMagicProperties')->andReturn(new Collection(['magicProperties']));
        $this->fixture->setParent($mock);

        $magicProperties = $this->fixture->getMagicProperties();
        $this->assertCount(2, $magicProperties);
    }

    /**
     * @covers ::getInheritedConstants
     */
    public function testGetInheritedConstantsNoParent() : void
    {
        $descriptor = new ClassDescriptor();
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedConstants());

        $descriptor->setParent(new stdClass());
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedConstants());
    }

    /**
     * @covers ::getInheritedConstants
     */
    public function testGetInheritedConstantsWithClassDescriptorParent() : void
    {
        $collectionMock = m::mock(Collection::class);
        $collectionMock->shouldReceive('get');
        $mock = m::mock(ClassDescriptor::class);
        $mock->shouldReceive('getConstants')->andReturn(new Collection(['constants']));
        $mock->shouldReceive('getInheritedConstants')->andReturn(new Collection(['inherited']));

        $this->fixture->setParent($mock);
        $result = $this->fixture->getInheritedConstants();

        $this->assertInstanceOf(Collection::class, $result);

        $expected = ['constants', 'inherited'];
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers ::getInheritedProperties
     */
    public function testGetInheritedPropertiesNoParent() : void
    {
        $descriptor = new ClassDescriptor();
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedProperties());

        $descriptor->setParent(new stdClass());
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedProperties());
    }

    /**
     * @covers ::getInheritedProperties
     */
    public function testGetInheritedPropertiesWithClassDescriptorParent() : void
    {
        $collectionMock = m::mock(Collection::class);
        $collectionMock->shouldReceive('get');
        $mock = m::mock(ClassDescriptor::class);
        $mock->shouldReceive('getProperties')->andReturn(new Collection(['properties']));
        $mock->shouldReceive('getInheritedProperties')->andReturn(new Collection(['inherited']));

        $this->fixture->setParent($mock);
        $result = $this->fixture->getInheritedProperties();

        $this->assertInstanceOf(Collection::class, $result);

        $expected = ['properties', 'inherited'];
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers ::getInheritedProperties
     */
    public function testRetrievingInheritedPropertiesReturnsTraitProperties() : void
    {
        // Arrange
        $expected = ['properties'];
        $traitDescriptorMock = m::mock(TraitDescriptor::class);
        $traitDescriptorMock->shouldReceive('getProperties')->andReturn(new Collection(['properties']));
        $this->fixture->setUsedTraits(new Collection([$traitDescriptorMock]));

        // Act
        $result = $this->fixture->getInheritedProperties();

        // Assert
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers ::getInheritedProperties
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
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame($expected, $result->getAll());
    }

    /**
     * @covers       ::getMagicMethods
     * @dataProvider provideMagicMethodProperties
     */
    public function testGetMagicMethods(bool $isStatic) : void
    {
        $methodName = 'methodName';
        $description = 'description';
        $response = new ReturnDescriptor('return');
        $response->setType(new String_());
        $argument = m::mock(ArgumentDescriptor::class);
        $argument->shouldReceive('setMethod');

        $this->assertEquals(0, $this->fixture->getMagicMethods()->count());

        $methodMock = m::mock(TagMethodDescriptor::class);
        $methodMock->shouldReceive('getMethodName')->andReturn($methodName);
        $methodMock->shouldReceive('getDescription')->andReturn($description);
        $methodMock->shouldReceive('getResponse')->andReturn($response);
        $methodMock->shouldReceive('getArguments')->andReturn(new Collection(['argument1' => $argument]));
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

        $mock = m::mock(ClassDescriptor::class);
        $mock->shouldReceive('getMagicMethods')->andReturn(new Collection(['magicMethods']));
        $this->fixture->setParent($mock);

        $magicMethods = $this->fixture->getMagicMethods();
        $this->assertCount(2, $magicMethods);
    }

    /**
     * Provider to test different properties for a class magic method
     * (provides isStatic)
     *
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
     * @covers ::setPackage
     */
    public function testSetPackage() : void
    {
        $package = 'Package';

        $mock = m::mock(ClassDescriptor::class);
        $mock->shouldDeferMissing();

        $constantDescriptor = m::mock(ConstantDescriptor::class);

        /** @var m\mockInterface|Collection $constantCollection */
        $constantCollection = m::mock(Collection::class);
        $constantCollection->shouldDeferMissing();
        $constantCollection->add($constantDescriptor);

        $propertyDescriptor = m::mock(PropertyDescriptor::class);

        /** @var m\mockInterface|Collection $propertyCollection */
        $propertyCollection = m::mock(Collection::class);
        $propertyCollection->shouldDeferMissing();
        $propertyCollection->add($propertyDescriptor);

        $methodDescriptor = m::mock(MethodDescriptor::class);

        /** @var m\mockInterface|Collection $methodCollection */
        $methodCollection = m::mock(Collection::class);
        $methodCollection->shouldDeferMissing();
        $methodCollection->add($methodDescriptor);

        /** @var m\mockInterface|ClassDescriptor $mock */
        $mock = m::mock(ClassDescriptor::class);
        $mock->shouldDeferMissing();
        $mock->shouldReceive('getProperties')->andReturn($propertyCollection);

        $mock->shouldReceive('getConstants')->andReturn($constantCollection);
        $constantDescriptor->shouldReceive('setPackage')->with($package);

        $mock->shouldReceive('getProperties')->andReturn($propertyCollection);
        $propertyDescriptor->shouldReceive('setPackage')->with($package);

        $mock->shouldReceive('getMethods')->andReturn($methodCollection);
        $methodDescriptor->shouldReceive('setPackage')->with($package);

        /** @var ClassDescriptor $mock */
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
        $this->assertInstanceOf(Collection::class, $this->fixture->__call('getNotexisting', []));
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getSummary
     */
    public function testSummaryInheritsWhenNoneIsPresent() : void
    {
        // Arrange
        $summary = 'This is a summary';
        $this->fixture->setSummary('');
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
        $this->fixture->setDescription('');
        $parentInterface = $this->whenFixtureHasParentClass();
        $parentInterface->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame($description, $result);
    }

    protected function whenFixtureHasParentClass() : ClassDescriptor
    {
        $class = new ClassDescriptor();
        $this->fixture->setParent($class);

        return $class;
    }
}
