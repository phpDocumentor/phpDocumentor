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
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;

/**
 * Tests the functionality for the InterfaceDescriptor class.
 * @coversDefaultClass \phpDocumentor\Descriptor\InterfaceDescriptor
 */
class InterfaceDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
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
     * @covers ::__construct
     */
    public function testInitialize()
    {
        $this->assertAttributeInstanceOf(Collection::class, 'parents', $this->fixture);
        $this->assertAttributeInstanceOf(Collection::class, 'constants', $this->fixture);
        $this->assertAttributeInstanceOf(Collection::class, 'methods', $this->fixture);
    }

    /**
     * @covers ::setParent
     * @covers ::getParent
     */
    public function testSettingAndGettingParentInterfaces()
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getParent());

        $mock = m::mock(Collection::class);

        $this->fixture->setParent($mock);

        $this->assertSame($mock, $this->fixture->getParent());
    }

    /**
     * @covers ::setConstants
     * @covers ::getConstants
     */
    public function testSettingAndGettingConstants()
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getConstants());

        $mock = m::mock(Collection::class);

        $this->fixture->setConstants($mock);

        $this->assertSame($mock, $this->fixture->getConstants());
    }

    /**
     * @covers ::setMethods
     * @covers ::getMethods
     */
    public function testSettingAndGettingMethods()
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMethods());

        $mock = m::mock(Collection::class);

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    /**
     * @covers ::getInheritedConstants
     */
    public function testGetInheritedConstantsNoParent()
    {
        $descriptor = new InterfaceDescriptor();
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedConstants());

        $descriptor->setParent(new \stdClass());
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedConstants());
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getSummary
     */
    public function testSummaryInheritsWhenNoneIsPresent()
    {
        // Arrange
        $summary = 'This is a summary';
        $this->fixture->setSummary(null);
        $parentInterface = $this->whenFixtureHasParentInterface();
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
        $parentInterface = $this->whenFixtureHasParentInterface();
        $parentInterface->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame($description, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getAuthor
     */
    public function testAuthorTagsInheritWhenNoneArePresent()
    {
        // Arrange
        $authorTagDescriptor = new AuthorDescriptor('author');
        $authorCollection = new Collection([$authorTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasParentInterface();
        $parentProperty->getTags()->set('author', $authorCollection);

        // Act
        $result = $this->fixture->getAuthor();

        // Assert
        $this->assertSame($authorCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getCopyright
     */
    public function testCopyrightTagsInheritWhenNoneArePresent()
    {
        // Arrange
        $copyrightTagDescriptor = new TagDescriptor('copyright');
        $copyrightCollection = new Collection([$copyrightTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasParentInterface();
        $parentProperty->getTags()->set('copyright', $copyrightCollection);

        // Act
        $result = $this->fixture->getCopyright();

        // Assert
        $this->assertSame($copyrightCollection, $result);
    }

    /**
     * @covers \phpDocumentor\Descriptor\DescriptorAbstract::getVersion
     */
    public function testVersionTagsInheritWhenNoneArePresent()
    {
        // Arrange
        $versionTagDescriptor = new VersionDescriptor('version');
        $versionCollection = new Collection([$versionTagDescriptor]);
        $this->fixture->getTags()->clear();
        $parentProperty = $this->whenFixtureHasParentInterface();
        $parentProperty->getTags()->set('version', $versionCollection);

        // Act
        $result = $this->fixture->getVersion();

        // Assert
        $this->assertSame($versionCollection, $result);
    }

    /**
     * @covers ::getInheritedConstants
     */
    public function testGetInheritedConstantsWithClassDescriptorParent()
    {
        $constantInParent = $this->givenConstantWithName('constant');
        $constantInGrandParent = $this->givenConstantWithName('constantInGrandParent');
        $constantInParentClass = $this->givenConstantWithName('constantInClass');

        $parentInterface = new InterfaceDescriptor();
        $parentInterface->setConstants(new Collection([$constantInParent]));

        $parentClass = new ClassDescriptor();
        $parentClass->setConstants(new Collection([$constantInParentClass]));

        $grandParentInterface = new InterfaceDescriptor();
        $grandParentInterface->setConstants(new Collection([$constantInGrandParent]));

        $parentInterface->setParent(new Collection([$grandParentInterface]));
        $this->fixture->setParent(new Collection([$parentInterface, $parentClass]));

        $result = $this->fixture->getInheritedConstants();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame([$constantInParent, $constantInGrandParent], $result->getAll());
    }

    /**
     * @covers ::getInheritedMethods
     */
    public function testRetrievingInheritedMethodsReturnsEmptyCollectionWithoutParent()
    {
        $inheritedMethods = $this->fixture->getInheritedMethods();
        $this->assertInstanceOf(Collection::class, $inheritedMethods);
        $this->assertCount(0, $inheritedMethods);
    }

    /**
     * @covers ::getInheritedMethods
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

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertSame([$parentDescriptor, $grandParentDescriptor], $result->getAll());
    }

    /**
     * @return InterfaceDescriptor
     */
    protected function whenFixtureHasParentInterface()
    {
        $interface = new InterfaceDescriptor();
        $this->fixture->getParent()->set('IA', $interface);

        return $interface;
    }

    private function givenConstantWithName(string $name): ConstantDescriptor
    {
        $constantInParent = new ConstantDescriptor();
        $constantInParent->setName($name);

        return $constantInParent;
    }
}
