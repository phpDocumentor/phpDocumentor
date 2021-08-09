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
use phpDocumentor\Reflection\Fqsen;

/**
 * Tests the functionality for the TraitDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\TraitDescriptor
 * @covers ::__construct
 */
final class TraitDescriptorTest extends MockeryTestCase
{
    /** @var TraitDescriptor $fixture */
    private $fixture;
    use MagicPropertyContainerTests;
    use MagicMethodContainerTests;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new TraitDescriptor();
        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\My\Trait'));
    }

    /**
     * @covers ::setProperties
     * @covers ::getProperties
     */
    public function testSettingAndGettingProperties(): void
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
    public function testSettingAndGettingMethods(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMethods());

        $collection = new Collection();

        $this->fixture->setMethods($collection);

        $this->assertSame($collection, $this->fixture->getMethods());
    }

    /**
     * @covers ::getInheritedMethods
     */
    public function testGetInheritedMethods(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getInheritedMethods());

        $collection = $this->fixture->getInheritedMethods();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers ::getInheritedProperties
     */
    public function testGetInheritedProperties(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getInheritedProperties());

        $collection = $this->fixture->getInheritedProperties();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers ::setPackage
     */
    public function testSettingAndGettingPackage(): void
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
    public function testSettingAndGettingUsedTraits(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getUsedTraits());

        $usedTraitsCollection = new Collection();
        $this->fixture->setUsedTraits($usedTraitsCollection);

        $this->assertSame($usedTraitsCollection, $this->fixture->getUsedTraits());
    }
}
