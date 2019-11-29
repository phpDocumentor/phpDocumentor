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
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Tests the functionality for the TraitDescriptor class.
 */
class TraitDescriptorTest extends MockeryTestCase
{
    /** @var TraitDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->markTestIncomplete('Something is off with this test, review it or rewrite it');
        $this->fixture = new TraitDescriptor();
    }

    /**
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::setProperties
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::getProperties
     */
    public function testSettingAndGettingProperties() : void
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getProperties());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setProperties($mock);

        $this->assertSame($mock, $this->fixture->getProperties());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::setMethods
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::getMethods
     */
    public function testSettingAndGettingMethods() : void
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMethods());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::getInheritedMethods
     */
    public function testGetInheritedMethods() : void
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getInheritedMethods());

        $collection = $this->fixture->getInheritedMethods();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::getMagicMethods
     */
    public function testMagicMethodsReturnsEmptyCollectionWhenNoTags() : void
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMagicMethods());

        $collection = $this->fixture->getMagicMethods();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::getMagicMethods
     * @dataProvider provideMagicMethodProperties
     */
    public function testMagicMethodsReturnsExpectedCollectionWithTags(bool $isStatic) : void
    {
        $mockMethodDescriptor = m::mock('phpDocumentor\Descriptor\Tag\MethodDescriptor');
        $mockMethodDescriptor->shouldReceive('getMethodName')->andReturn('Sample');
        $mockMethodDescriptor->shouldReceive('isStatic')->andReturn($isStatic);
        $mockMethodDescriptor->shouldReceive('getDescription')->andReturn('Sample description');

        $methodCollection = new Collection([$mockMethodDescriptor]);
        $this->fixture->getTags()->set('method', $methodCollection);

        $magicMethodsCollection = $this->fixture->getMagicMethods();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $magicMethodsCollection);
        $this->assertSame(1, $magicMethodsCollection->count());
        $this->assertSame('Sample', $magicMethodsCollection[0]->getName());
        $this->assertSame('Sample description', $magicMethodsCollection[0]->getDescription());
        $this->assertSame($isStatic, $magicMethodsCollection[0]->isStatic());
        $this->assertSame($this->fixture, $magicMethodsCollection[0]->getParent());
    }

    /**
     * Provider to test different properties for a trait magic method
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
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::getInheritedProperties
     */
    public function testGetInheritedProperties() : void
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getInheritedProperties());

        $collection = $this->fixture->getInheritedProperties();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::getMagicProperties
     */
    public function testMagicPropertiesReturnsEmptyCollectionWhenNoTags() : void
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMagicProperties());

        $collection = $this->fixture->getMagicProperties();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::getMagicProperties
     */
    public function testMagicPropertiesReturnsExpectedCollectionWithTags() : void
    {
        $mockTagPropertyDescriptor = m::mock('phpDocumentor\Descriptor\Tag\PropertyDescriptor');
        $mockTagPropertyDescriptor->shouldReceive('getVariableName')->andReturn('Sample');
        $mockTagPropertyDescriptor->shouldReceive('getDescription')->andReturn('Sample description');
        $mockTagPropertyDescriptor->shouldReceive('getType')->andReturn(new Mixed_());

        $propertyCollection = new Collection([$mockTagPropertyDescriptor]);
        $this->fixture->getTags()->set('property', $propertyCollection);

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMagicProperties());

        $magicPropertiesCollection = $this->fixture->getMagicProperties();
        $this->assertSame(1, $magicPropertiesCollection->count());
        $this->assertSame('Sample', $magicPropertiesCollection[0]->getName());
        $this->assertSame('Sample description', $magicPropertiesCollection[0]->getDescription());
        $this->assertEquals(new Mixed_(), $magicPropertiesCollection[0]->getType());
        $this->assertSame($this->fixture, $magicPropertiesCollection[0]->getParent());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::setPackage
     */
    public function testSettingAndGettingPackage() : void
    {
        $package                = new PackageDescriptor();
        $mockPropertyDescriptor = m::mock('phpDocumentor\Descriptor\PropertyDescriptor');
        $mockPropertyDescriptor->shouldReceive('setPackage')->with($package);

        $mockMethodDescriptor = m::mock('phpDocumentor\Descriptor\MethodDescriptor');
        $mockMethodDescriptor->shouldReceive('setPackage')->with($package);

        $propertyCollection = new Collection([$mockPropertyDescriptor]);
        $methodCollection   = new Collection([$mockMethodDescriptor]);
        $this->fixture->setProperties($propertyCollection);
        $this->fixture->setMethods($methodCollection);

        $this->fixture->setPackage($package);

        $this->assertSame($package, $this->fixture->getPackage());
    }

    /**
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::getUsedTraits
     * @covers \phpDocumentor\Descriptor\TraitDescriptor::setUsedTraits
     */
    public function testSettingAndGettingUsedTraits() : void
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getUsedTraits());

        $usedTraitsCollection = new Collection();
        $this->fixture->setUsedTraits($usedTraitsCollection);

        $this->assertSame($usedTraitsCollection, $this->fixture->getUsedTraits());
    }
}
