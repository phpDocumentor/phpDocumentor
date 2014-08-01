<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use \Mockery as m;

/**
 * Tests the functionality for the TraitDescriptor class.
 */
class TraitDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var TraitDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new TraitDescriptor();
    }

    /**
     * Tests whether all collection objects are properly initialized.
     *
     * @covers phpDocumentor\Descriptor\TraitDescriptor::__construct
     */
    public function testInitialize()
    {
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'properties', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'methods', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\TraitDescriptor::setProperties
     * @covers phpDocumentor\Descriptor\TraitDescriptor::getProperties
     */
    public function testSettingAndGettingProperties()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getProperties());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setProperties($mock);

        $this->assertSame($mock, $this->fixture->getProperties());
    }

    /**
     * @covers phpDocumentor\Descriptor\TraitDescriptor::setMethods
     * @covers phpDocumentor\Descriptor\TraitDescriptor::getMethods
     */
    public function testSettingAndGettingMethods()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMethods());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    /**
     * @covers phpDocumentor\Descriptor\TraitDescriptor::getInheritedMethods
     */
    public function testGetInheritedMethods()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getInheritedMethods());

        $collection = $this->fixture->getInheritedMethods();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers phpDocumentor\Descriptor\TraitDescriptor::getMagicMethods
     */
    public function testMagicMethodsReturnsEmptyCollectionWhenNoTags()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMagicMethods());

        $collection = $this->fixture->getMagicMethods();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers phpDocumentor\Descriptor\TraitDescriptor::getMagicMethods
     */
    public function testMagicMethodsReturnsExpectedCollectionWithTags()
    {
        $mockMethodDescriptor = m::mock('phpDocumentor\Descriptor\Tag\MethodDescriptor');
        $mockMethodDescriptor->shouldReceive('getMethodName')->andReturn('Sample');
        $mockMethodDescriptor->shouldReceive('getDescription')->andReturn('Sample description');

        $methodCollection = new Collection(array($mockMethodDescriptor));
        $this->fixture->getTags()->set('method', $methodCollection);

        $magicMethodsCollection = $this->fixture->getMagicMethods();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $magicMethodsCollection);
        $this->assertSame(1, $magicMethodsCollection->count());
        $this->assertSame('Sample', $magicMethodsCollection[0]->getName());
        $this->assertSame('Sample description', $magicMethodsCollection[0]->getDescription());
        $this->assertSame($this->fixture, $magicMethodsCollection[0]->getParent());
    }

    /**
     * @covers phpDocumentor\Descriptor\TraitDescriptor::getInheritedProperties
     */
    public function testGetInheritedProperties()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getInheritedProperties());

        $collection = $this->fixture->getInheritedProperties();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers phpDocumentor\Descriptor\TraitDescriptor::getMagicProperties
     */
    public function testMagicPropertiesReturnsEmptyCollectionWhenNoTags()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMagicProperties());

        $collection = $this->fixture->getMagicProperties();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @covers phpDocumentor\Descriptor\TraitDescriptor::getMagicProperties
     */
    public function testMagicPropertiesReturnsExpectedCollectionWithTags()
    {
        $mockTagPropertyDescriptor = m::mock('phpDocumentor\Descriptor\Tag\PropertyDescriptor');
        $mockTagPropertyDescriptor->shouldReceive('getVariableName')->andReturn('Sample');
        $mockTagPropertyDescriptor->shouldReceive('getDescription')->andReturn('Sample description');
        $mockTagPropertyDescriptor->shouldReceive('getTypes')->andReturn(new Collection);

        $propertyCollection = new Collection(array($mockTagPropertyDescriptor));
        $this->fixture->getTags()->set('property', $propertyCollection);

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMagicProperties());

        $magicPropertiesCollection = $this->fixture->getMagicProperties();
        $this->assertSame(1, $magicPropertiesCollection->count());
        $this->assertSame('Sample', $magicPropertiesCollection[0]->getName());
        $this->assertSame('Sample description', $magicPropertiesCollection[0]->getDescription());
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $magicPropertiesCollection[0]->getTypes());
        $this->assertSame(0, $magicPropertiesCollection[0]->getTypes()->count());
        $this->assertSame($this->fixture, $magicPropertiesCollection[0]->getParent());
    }

    /**
     * @covers phpDocumentor\Descriptor\TraitDescriptor::setPackage
     */
    public function testSettingAndGettingPackage()
    {
        $package = new \phpDocumentor\Descriptor\PackageDescriptor();
        $mockPropertyDescriptor = m::mock('phpDocumentor\Descriptor\PropertyDescriptor');
        $mockPropertyDescriptor->shouldReceive('setPackage')->with($package);

        $mockMethodDescriptor = m::mock('phpDocumentor\Descriptor\MethodDescriptor');
        $mockMethodDescriptor->shouldReceive('setPackage')->with($package);

        $propertyCollection = new Collection(array($mockPropertyDescriptor));
        $methodCollection = new Collection(array($mockMethodDescriptor));
        $this->fixture->setProperties($propertyCollection);
        $this->fixture->setMethods($methodCollection);

        $this->fixture->setPackage($package);

        $this->assertSame($package, $this->fixture->getPackage());
    }

    /**
     * @covers phpDocumentor\Descriptor\TraitDescriptor::getUsedTraits
     * @covers phpDocumentor\Descriptor\TraitDescriptor::setUsedTraits
     */
    public function testSettingAndGettingUsedTraits()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getUsedTraits());

        $usedTraitsCollection = new Collection;
        $this->fixture->setUsedTraits($usedTraitsCollection);

        $this->assertSame($usedTraitsCollection, $this->fixture->getUsedTraits());
    }
}
