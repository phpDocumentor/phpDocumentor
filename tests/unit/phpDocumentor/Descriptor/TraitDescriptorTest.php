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
    use MagicPropertyContainerTests;
    use MagicMethodContainerTests;

    private TraitDescriptor $fixture;

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
    public function testSettingAndGettingConstants(): void
    {
        $collection = new Collection();

        $this->fixture->setConstants($collection);

        $this->assertSame($collection, $this->fixture->getConstants());
    }

    /**
     * @covers ::setProperties
     * @covers ::getProperties
     */
    public function testSettingAndGettingProperties(): void
    {
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
        $collection = new Collection();

        $this->fixture->setMethods($collection);

        $this->assertSame($collection, $this->fixture->getMethods());
    }

    /**
     * @covers ::setPackage
     */
    public function testSettingAndGettingPackage(): void
    {
        $package = new PackageDescriptor();

        $propertyDescriptor = new PropertyDescriptor();
        $propertyDescriptor->setPackage($package);

        $methodDescriptor = new MethodDescriptor();
        $methodDescriptor->setPackage($package);

        $constantDescriptor = new ConstantDescriptor();
        $constantDescriptor->setPackage($package);

        $this->fixture->setConstants(new Collection([$constantDescriptor]));
        $this->fixture->setProperties(new Collection([$propertyDescriptor]));
        $this->fixture->setMethods(new Collection([$methodDescriptor]));

        $this->fixture->setPackage($package);

        $this->assertSame($package, $this->fixture->getPackage());
        $this->assertSame($package, $this->fixture->getConstants()->first()->getPackage());
        $this->assertSame($package, $this->fixture->getProperties()->first()->getPackage());
        $this->assertSame($package, $this->fixture->getMethods()->first()->getPackage());
    }

    /**
     * @covers ::getUsedTraits
     * @covers ::setUsedTraits
     */
    public function testSettingAndGettingUsedTraits(): void
    {
        $usedTraitsCollection = new Collection();
        $this->fixture->setUsedTraits($usedTraitsCollection);

        $this->assertSame($usedTraitsCollection, $this->fixture->getUsedTraits());
    }
}
