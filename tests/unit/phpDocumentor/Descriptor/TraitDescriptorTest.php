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
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Tests the functionality for the TraitDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\TraitDescriptor
 */
final class TraitDescriptorTest extends MockeryTestCase
{
    /** @var TraitDescriptor $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new TraitDescriptor();
        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\My\Trait'));
    }

    /**
     * @covers ::setProperties
     * @covers ::getProperties
     */
    public function testSettingAndGettingProperties() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getProperties());

        $collection = new Collection();

        $this->fixture->setProperties($collection);

        $this->assertSame($collection, $this->fixture->getProperties());
    }

    /**
     * @covers ::setMethods
     * @covers ::getMethods
     */
    public function testSettingAndGettingMethods() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMethods());

        $collection = new Collection();

        $this->fixture->setMethods($collection);

        $this->assertSame($collection, $this->fixture->getMethods());
    }

    /**
     * @covers ::getInheritedMethods
     */
    public function testGetInheritedMethods() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getInheritedMethods());

        $collection = $this->fixture->getInheritedMethods();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers ::getMagicMethods
     */
    public function testMagicMethodsReturnsEmptyCollectionWhenNoTags() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMagicMethods());

        $collection = $this->fixture->getMagicMethods();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers ::getMagicMethods
     * @dataProvider provideMagicMethodProperties
     */
    public function testMagicMethodsReturnsExpectedCollectionWithTags(bool $isStatic) : void
    {
        $mockMethodDescriptor = m::mock(TagMethodDescriptor::class);
        $mockMethodDescriptor->shouldReceive('getMethodName')->andReturn('Sample');
        $mockMethodDescriptor->shouldReceive('isStatic')->andReturn($isStatic);
        $mockMethodDescriptor->shouldReceive('getDescription')->andReturn('Sample description');

        $methodCollection = new Collection([$mockMethodDescriptor]);
        $this->fixture->getTags()->set('method', $methodCollection);

        $magicMethodsCollection = $this->fixture->getMagicMethods();
        $this->assertInstanceOf(Collection::class, $magicMethodsCollection);
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
     * @covers ::getInheritedProperties
     */
    public function testGetInheritedProperties() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getInheritedProperties());

        $collection = $this->fixture->getInheritedProperties();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers ::getMagicProperties
     */
    public function testMagicPropertiesReturnsEmptyCollectionWhenNoTags() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMagicProperties());

        $collection = $this->fixture->getMagicProperties();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers ::getMagicProperties
     * @dataProvider provideMagicProperties
     */
    public function testMagicPropertiesReturnsExpectedCollectionWithTags(
        string $name,
        string $description,
        Type $type
    ) : void {
        $mockTagPropertyDescriptor = m::mock(TagPropertyDescriptor::class);
        $mockTagPropertyDescriptor->shouldReceive('getVariableName')->andReturn($name);
        $mockTagPropertyDescriptor->shouldReceive('getDescription')->andReturn($description);
        $mockTagPropertyDescriptor->shouldReceive('getType')->andReturn($type);

        $propertyCollection = new Collection([$mockTagPropertyDescriptor]);
        $this->fixture->getTags()->set('property', $propertyCollection);

        $this->assertInstanceOf(Collection::class, $this->fixture->getMagicProperties());

        $magicPropertiesCollection = $this->fixture->getMagicProperties();
        $this->assertSame(1, $magicPropertiesCollection->count());
        $this->assertSame($name, $magicPropertiesCollection[0]->getName());
        $this->assertSame($description, $magicPropertiesCollection[0]->getDescription());
        $this->assertEquals($type, $magicPropertiesCollection[0]->getType());
        $this->assertSame($this->fixture, $magicPropertiesCollection[0]->getParent());
    }

    /**
     * @return array<string, mixed[]>
     */
    public function provideMagicProperties() : array
    {
        return [
            'normal sample' => [
                'name' => 'Sample',
                'description' => 'Sample Description',
                'type' => new Mixed_(),
            ],
            'without name' => [
                'name' => '',
                'description' => 'Sample Description',
                'type' => new Mixed_(),
            ],
        ];
    }

    /**
     * @covers ::setPackage
     */
    public function testSettingAndGettingPackage() : void
    {
        $package = new PackageDescriptor();
        $mockPropertyDescriptor = m::mock(PropertyDescriptor::class);
        $mockPropertyDescriptor->shouldReceive('setPackage')->with($package);

        $mockMethodDescriptor = m::mock(MethodDescriptor::class);
        $mockMethodDescriptor->shouldReceive('setPackage')->with($package);

        $propertyCollection = new Collection([$mockPropertyDescriptor]);
        $methodCollection = new Collection([$mockMethodDescriptor]);
        $this->fixture->setProperties($propertyCollection);
        $this->fixture->setMethods($methodCollection);

        $this->fixture->setPackage($package);

        $this->assertSame($package, $this->fixture->getPackage());
    }

    /**
     * @covers ::getUsedTraits
     * @covers ::setUsedTraits
     */
    public function testSettingAndGettingUsedTraits() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getUsedTraits());

        $usedTraitsCollection = new Collection();
        $this->fixture->setUsedTraits($usedTraitsCollection);

        $this->assertSame($usedTraitsCollection, $this->fixture->getUsedTraits());
    }
}
