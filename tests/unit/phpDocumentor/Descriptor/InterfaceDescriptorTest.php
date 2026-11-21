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

use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * Tests the functionality for the InterfaceDescriptor class.
 */
#[CoversClass(InterfaceDescriptor::class)]
#[CoversClass(DescriptorAbstract::class)]
final class InterfaceDescriptorTest extends TestCase
{
    use ProphecyTrait;

    private InterfaceDescriptor $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new InterfaceDescriptor();
    }

    public function testSettingAndGettingParentInterfaces(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getParent());

        $collection = new Collection();

        $this->fixture->setParent($collection);

        $this->assertSame($collection, $this->fixture->getParent());
    }

    public function testSettingAndGettingConstants(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getConstants());

        $mock = $this->prophesize(Collection::class)->reveal();

        $this->fixture->setConstants($mock);

        $this->assertSame($mock, $this->fixture->getConstants());
    }

    public function testSettingAndGettingProperties(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getProperties());

        $mock = $this->prophesize(Collection::class)->reveal();

        $this->fixture->setProperties($mock);

        $this->assertSame($mock, $this->fixture->getProperties());
    }

    public function testSettingAndGettingMethods(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMethods());

        $mock = $this->prophesize(Collection::class)->reveal();

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    public function testGetInheritedConstantsNoParent(): void
    {
        $descriptor = new InterfaceDescriptor();
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedConstants());

        $descriptor->setParent(new Collection());
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedConstants());
    }

    public function testSummaryInheritsWhenNoneIsPresent(): void
    {
        // Arrange
        $summary = 'This is a summary';
        $this->fixture->setSummary('');
        $parentInterface = $this->whenFixtureHasParentInterface();
        $parentInterface->setSummary($summary);

        // Act
        $result = $this->fixture->getSummary();

        // Assert
        $this->assertSame($summary, $result);
    }

    public function testAuthorTagsInheritWhenNoneArePresent(): void
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

    public function testCopyrightTagsInheritWhenNoneArePresent(): void
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

    public function testVersionTagsInheritWhenNoneArePresent(): void
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

    public function testGetInheritedConstantsWithClassDescriptorParent(): void
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
        $this->assertSame([$constantInGrandParent, $constantInParent], $result->getAll());
    }

    public function testGetInheritedPropertiesNoParent(): void
    {
        $descriptor = new InterfaceDescriptor();
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedProperties());

        $descriptor->setParent(new Collection());
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedProperties());
    }

    public function testGetInheritedPropertiesWithParentInterface(): void
    {
        $propertyInParent = $this->givenPropertyWithName('property');
        $propertyInGrandParent = $this->givenPropertyWithName('propertyInGrandParent');

        $parentInterface = new InterfaceDescriptor();
        $parentInterface->setProperties(new Collection([$propertyInParent]));

        $grandParentInterface = new InterfaceDescriptor();
        $grandParentInterface->setProperties(new Collection([$propertyInGrandParent]));

        $parentInterface->setParent(new Collection([$grandParentInterface]));
        $this->fixture->setParent(new Collection([$parentInterface]));

        $result = $this->fixture->getInheritedProperties();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame([$propertyInGrandParent, $propertyInParent], $result->getAll());
    }

    public function testGetInheritedPropertiesWithMultipleParentInterfaces(): void
    {
        $propertyInFirstParent = $this->givenPropertyWithName('propertyInFirstParent');
        $propertyInSecondParent = $this->givenPropertyWithName('propertyInSecondParent');

        $firstParent = new InterfaceDescriptor();
        $firstParent->setProperties(new Collection([$propertyInFirstParent]));

        $secondParent = new InterfaceDescriptor();
        $secondParent->setProperties(new Collection([$propertyInSecondParent]));

        $this->fixture->setParent(new Collection([$firstParent, $secondParent]));

        $result = $this->fixture->getInheritedProperties();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame([$propertyInSecondParent, $propertyInFirstParent], $result->getAll());
    }

    public function testRetrievingInheritedMethodsReturnsEmptyCollectionWithoutParent(): void
    {
        $inheritedMethods = $this->fixture->getInheritedMethods();
        $this->assertInstanceOf(Collection::class, $inheritedMethods);
        $this->assertCount(0, $inheritedMethods);
    }

    public function testRetrievingInheritedMethodsReturnsCollectionWithParent(): void
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

        $this->assertSame([$grandParentDescriptor, $parentDescriptor], $result->getAll());
    }

    private function whenFixtureHasParentInterface(): InterfaceDescriptor
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

    private function givenPropertyWithName(string $name): PropertyDescriptor
    {
        $property = new PropertyDescriptor();
        $property->setName($name);

        return $property;
    }
}
