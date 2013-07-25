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

/**
 * Tests the functionality for the InterfaceDescriptor class.
 */
class InterfaceDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var InterfaceDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new InterfaceDescriptor();
    }

    /**
     * Tests whether all collection objects are properly initialized.
     *
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::__construct
     */
    public function testInitialize()
    {
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'parents', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'constants', $this->fixture);
        $this->assertAttributeInstanceOf('phpDocumentor\Descriptor\Collection', 'methods', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::setParent
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::getParent
     */
    public function testSettingAndGettingParentInterfaces()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getParent());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setParent($mock);

        $this->assertSame($mock, $this->fixture->getParent());
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::setConstants
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::getConstants
     */
    public function testSettingAndGettingConstants()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getConstants());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setConstants($mock);

        $this->assertSame($mock, $this->fixture->getConstants());
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::setMethods
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::getMethods
     */
    public function testSettingAndGettingMethods()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getMethods());

        $mock = m::mock('phpDocumentor\Descriptor\Collection');

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::getInheritedConstants
     */
    public function testGetInheritedConstantsNoParent()
    {
        $descriptor = new InterfaceDescriptor();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $descriptor->getInheritedConstants());

        $descriptor->setParent(new \stdClass());
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $descriptor->getInheritedConstants());
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::getInheritedConstants
     */
    public function testGetInheritedConstantsWithClassDescriptorParent()
    {
        $parentDescriptor = new ConstantDescriptor();
        $parentDescriptor->setName('parent');
        $parentDescriptorCollection = new Collection();
        $parentDescriptorCollection->add($parentDescriptor);
        $parent = new InterfaceDescriptor();
        $parent->setConstants($parentDescriptorCollection);

        $grandParentDescriptor = new ConstantDescriptor();
        $grandParentDescriptor->setName('grandparent');
        $grandParentDescriptorCollection = new Collection();
        $grandParentDescriptorCollection->add($grandParentDescriptor);
        $grandParent = new InterfaceDescriptor();
        $grandParent->setConstants($grandParentDescriptorCollection);

        $grandParentCollection = new Collection();
        $grandParentCollection->add($grandParent);
        $parent->setParent($grandParentCollection);

        $parentCollection = new Collection();
        $parentCollection->add($parent);

        $this->fixture->setParent($parentCollection);
        $result = $this->fixture->getInheritedConstants();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $this->assertSame(array($parentDescriptor, $grandParentDescriptor), $result->getAll());
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsEmptyCollectionWithoutParent()
    {
        $inheritedMethods = $this->fixture->getInheritedMethods();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $inheritedMethods);
        $this->assertCount(0, $inheritedMethods);
    }

    /**
     * @covers phpDocumentor\Descriptor\InterfaceDescriptor::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsCollectionWithParent()
    {
        $parentDescriptor = new MethodDescriptor();
        $parentDescriptor->setName('parent');
        $parentDescriptorCollection = new Collection();
        $parentDescriptorCollection->add($parentDescriptor);
        $parent = new InterfaceDescriptor();
        $parent->setMethods($parentDescriptorCollection);
        $parentCollection = new Collection();
        $parentCollection->add($parent);

        $grandParentDescriptor = new MethodDescriptor();
        $grandParentDescriptor->setName('grandparent');
        $grandParentDescriptorCollection = new Collection();
        $grandParentDescriptorCollection->add($grandParentDescriptor);
        $grandParent = new InterfaceDescriptor();
        $grandParent->setMethods($grandParentDescriptorCollection);
        $grandParentCollection = new Collection();
        $grandParentCollection->add($grandParent);

        $parent->setParent($grandParentCollection);

        $this->fixture->setParent($parentCollection);
        $result = $this->fixture->getInheritedMethods();

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $this->assertSame(array($parentDescriptor, $grandParentDescriptor), $result->getAll());
    }

}
