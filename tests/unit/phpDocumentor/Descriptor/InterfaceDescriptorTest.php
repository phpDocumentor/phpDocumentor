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
use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;

/**
 * Tests the functionality for the InterfaceDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\InterfaceDescriptor
 */
final class InterfaceDescriptorTest extends MockeryTestCase
{
    private InterfaceDescriptor $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new InterfaceDescriptor();
    }

    /**
     * @covers ::setParent
     * @covers ::getParent
     */
    public function testSettingAndGettingParentInterfaces(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getParent());

        $collection = new Collection();

        $this->fixture->setParent($collection);

        $this->assertSame($collection, $this->fixture->getParent());
    }

    /**
     * @covers ::setConstants
     * @covers ::getConstants
     */
    public function testSettingAndGettingConstants(): void
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
    public function testSettingAndGettingMethods(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMethods());

        $mock = m::mock(Collection::class);

        $this->fixture->setMethods($mock);

        $this->assertSame($mock, $this->fixture->getMethods());
    }

    /** @covers ::getInheritedConstants */
    public function testGetInheritedConstantsNoParent(): void
    {
        $descriptor = new InterfaceDescriptor();
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedConstants());

        $descriptor->setParent(new Collection());
        $this->assertInstanceOf(Collection::class, $descriptor->getInheritedConstants());
    }

    /** @covers \phpDocumentor\Descriptor\DescriptorAbstract::getSummary */
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

    /** @covers \phpDocumentor\Descriptor\DescriptorAbstract::getAuthor */
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

    /** @covers \phpDocumentor\Descriptor\DescriptorAbstract::getCopyright */
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

    /** @covers \phpDocumentor\Descriptor\DescriptorAbstract::getVersion */
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

    /** @covers ::getInheritedConstants */
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
        $this->assertSame([$constantInParent, $constantInGrandParent], $result->getAll());
    }

    /** @covers ::getInheritedMethods */
    public function testRetrievingInheritedMethodsReturnsEmptyCollectionWithoutParent(): void
    {
        $inheritedMethods = $this->fixture->getInheritedMethods();
        $this->assertInstanceOf(Collection::class, $inheritedMethods);
        $this->assertCount(0, $inheritedMethods);
    }

    /** @covers ::getInheritedMethods */
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

        $this->assertSame([$parentDescriptor, $grandParentDescriptor], $result->getAll());
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
}
