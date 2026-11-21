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

use phpDocumentor\Descriptor\ValueObjects\Visibility;
use phpDocumentor\Descriptor\ValueObjects\VisibilityModifier;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Ticket;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Tests the functionality for the ClassDescriptor class.
 */
#[CoversClass(ClassDescriptor::class)]
#[CoversClass(DescriptorAbstract::class)]
final class ClassDescriptorTest extends TestCase
{
    protected ClassDescriptor $fixture;
    use MagicPropertyContainerTests;
    use MagicMethodContainerTests;
    use TraitUsageTests;
    use AttributedTestTrait;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new ClassDescriptor();
        $this->fixture->setFullyQualifiedStructuralElementName(new Fqsen('\My\Class'));
    }

    private function getParent(): ClassDescriptor
    {
        $parent = new ClassDescriptor();
        $parent->setFullyQualifiedStructuralElementName(new Fqsen('\My\Parent'));

        return $parent;
    }

    public function testSettingAndGettingAParent(): void
    {
        self::assertNull($this->fixture->getParent());

        $mock = $this->prophesize(ClassDescriptor::class)->reveal();

        $this->fixture->setParent($mock);

        self::assertSame($mock, $this->fixture->getParent());
    }

    public function testSettingNoParent(): void
    {
        $mock = null;

        $this->fixture->setParent($mock);

        self::assertSame($mock, $this->fixture->getParent());
    }

    public function testSettingAndGettingInterfaces(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getInterfaces());

        $mock = $this->prophesize(Collection::class)->reveal();

        $this->fixture->setInterfaces($mock);

        self::assertSame($mock, $this->fixture->getInterfaces());
    }

    public function testSettingAndGettingConstants(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getConstants());

        $mock = $this->prophesize(Collection::class)->reveal();

        $this->fixture->setConstants($mock);

        self::assertSame($mock, $this->fixture->getConstants());
    }

    public function testSettingAndGettingProperties(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getProperties());

        $mock = $this->prophesize(Collection::class)->reveal();

        $this->fixture->setProperties($mock);

        self::assertSame($mock, $this->fixture->getProperties());
    }

    public function testSettingAndGettingMethods(): void
    {
        self::assertInstanceOf(Collection::class, $this->fixture->getMethods());

        $mock = $this->prophesize(Collection::class)->reveal();

        $this->fixture->setMethods($mock);

        self::assertSame($mock, $this->fixture->getMethods());
    }

    public function testRetrievingInheritedMethodsReturnsEmptyCollectionWithoutParent(): void
    {
        $inheritedMethods = $this->fixture->getInheritedMethods();
        self::assertInstanceOf(Collection::class, $inheritedMethods);
        self::assertCount(0, $inheritedMethods);
    }

    public function testRetrievingInheritedMethodsReturnsCollectionWithParent(): void
    {
        $grandParent = new ClassDescriptor();
        $grandParent->getMethods()->set('construct', new MethodDescriptor());

        $privateMethod = new MethodDescriptor();
        $privateMethod->setVisibility(new Visibility(VisibilityModifier::PRIVATE));

        $parent = new ClassDescriptor();
        $parent->getMethods()->set('construct', new MethodDescriptor());
        $parent->getMethods()->set('otherMethod', new MethodDescriptor());
        $parent->getMethods()->set('privateMethod', $privateMethod);
        $parent->setParent($grandParent);
        $this->fixture->setParent($parent);

        $expected = [];
        $expected['construct'] = $parent->getMethods()->get('construct');
        $expected['otherMethod'] = $parent->getMethods()->get('otherMethod');

        $result = $this->fixture->getInheritedMethods();
        self::assertSame($expected, $result->getAll());
    }

    public function testSettingAndGettingWhetherClassIsAbstract(): void
    {
        self::assertFalse($this->fixture->isAbstract());

        $this->fixture->setAbstract(true);

        self::assertTrue($this->fixture->isAbstract());
    }

    public function testSettingAndGettingWhetherClassIsFinal(): void
    {
        self::assertFalse($this->fixture->isFinal());

        $this->fixture->setFinal(true);

        self::assertTrue($this->fixture->isFinal());
    }

    public function testSettingAndGettingWhetherClassIsReadOnly(): void
    {
        self::assertFalse($this->fixture->isReadOnly());

        $this->fixture->setReadOnly(true);

        self::assertTrue($this->fixture->isReadOnly());
    }

    public function testGetInheritedConstantsNoParent(): void
    {
        $descriptor = new ClassDescriptor();
        self::assertInstanceOf(Collection::class, $descriptor->getInheritedConstants());

        $descriptor->setParent(new stdClass());
        self::assertInstanceOf(Collection::class, $descriptor->getInheritedConstants());
    }

    public function testGetInheritedConstantsWithClassDescriptorParent(): void
    {
        $mock = $this->prophesize(ClassDescriptor::class);
        $mock->getConstants()->willReturn(new Collection([new ConstantDescriptor()]));
        $mock->getInheritedConstants()->willReturn(new Collection([new ConstantDescriptor()]));

        $this->fixture->setParent($mock->reveal());
        $result = $this->fixture->getInheritedConstants();

        self::assertInstanceOf(Collection::class, $result);

        self::assertCount(2, $result->getAll());
    }

    public function testGetInheritedPropertiesNoParent(): void
    {
        $descriptor = new ClassDescriptor();
        self::assertInstanceOf(Collection::class, $descriptor->getInheritedProperties());

        $descriptor->setParent(new stdClass());
        self::assertInstanceOf(Collection::class, $descriptor->getInheritedProperties());
    }

    public function testGetInheritedPropertiesWithClassDescriptorParent(): void
    {
        $privateProperty = new PropertyDescriptor();
        $privateProperty->setVisibility(new Visibility(VisibilityModifier::PRIVATE));

        $mock = $this->prophesize(ClassDescriptor::class);
        $mock->getProperties()->willReturn(new Collection([new PropertyDescriptor(), $privateProperty]));
        $mock->getInheritedProperties()->willReturn(new Collection([new PropertyDescriptor()]));

        $this->fixture->setParent($mock->reveal());
        $result = $this->fixture->getInheritedProperties();

        self::assertInstanceOf(Collection::class, $result);

        self::assertCount(2, $result->getAll());
    }

    public function testRetrievingInheritedPropertiesReturnsTraitProperties(): void
    {
        // Arrange
        $expected = ['properties'];
        $traitDescriptorMock = $this->prophesize(TraitDescriptor::class);
        $traitDescriptorMock->getProperties()->willReturn(new Collection(['properties']));
        $this->fixture->setUsedTraits(new Collection([$traitDescriptorMock->reveal()]));

        // Act
        $result = $this->fixture->getInheritedProperties();

        // Assert
        self::assertInstanceOf(Collection::class, $result);
        self::assertSame($expected, $result->getAll());
    }

    #[Ticket('https://github.com/phpDocumentor/phpDocumentor2/issues/1307')]
    public function testRetrievingInheritedPropertiesDoesNotCrashWhenUsedTraitIsNotInProject(): void
    {
        // Arrange
        $expected = [];
        // unknown traits are not converted to TraitDescriptors but kept as strings
        $this->fixture->setUsedTraits(new Collection(['unknownTrait']));

        // Act
        $result = $this->fixture->getInheritedProperties();

        // Assert
        self::assertInstanceOf(Collection::class, $result);
        self::assertSame($expected, $result->getAll());
    }

    public function testSetPackage(): void
    {
        $package = 'Package';

        $constantDescriptor = new ConstantDescriptor();
        $propertyDescriptor = new PropertyDescriptor();
        $methodDescriptor = new MethodDescriptor();
        $mock = new ClassDescriptor();
        $mock->setConstants(new Collection([$constantDescriptor]));
        $mock->setProperties(new Collection([$propertyDescriptor]));
        $mock->setMethods(new Collection([$methodDescriptor]));

        $mock->setPackage($package);

        self::assertTrue(true);
    }

    /**
     * Test to cover magic method of parent abstract class
     */
    public function testCall(): void
    {
        self::assertNull($this->fixture->__call('notexisting', []));
        self::assertInstanceOf(Collection::class, $this->fixture->__call('getNotexisting', []));
    }

    public function testSummaryInheritsWhenNoneIsPresent(): void
    {
        // Arrange
        $summary = 'This is a summary';
        $this->fixture->setSummary('');
        $parentInterface = $this->whenFixtureHasParentClass();
        $parentInterface->setSummary($summary);

        // Act
        $result = $this->fixture->getSummary();

        // Assert
        self::assertSame($summary, $result);
    }

    protected function whenFixtureHasParentClass(): ClassDescriptor
    {
        $class = new ClassDescriptor();
        $this->fixture->setParent($class);

        return $class;
    }
}
