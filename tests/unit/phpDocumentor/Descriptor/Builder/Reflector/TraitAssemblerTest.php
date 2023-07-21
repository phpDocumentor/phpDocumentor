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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\Trait_;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

use function sprintf;

/**
 * @uses \phpDocumentor\Descriptor\DescriptorAbstract
 * @uses \phpDocumentor\Descriptor\TraitDescriptor
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\TraitAssembler
 * @covers ::<private>
 */
final class TraitAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    private ObjectProphecy $builder;

    private TraitAssembler $assembler;

    public function setUp(): void
    {
        $this->builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->assembler = new TraitAssembler();
        $this->assembler->setBuilder($this->builder->reveal());
    }

    /** @covers ::create */
    public function testTraitWillHaveAnFqsenNameAndNamespace(): void
    {
        $fqsen = new Fqsen('\My\Space\MyTrait');
        $reflectedTrait = new Trait_($fqsen);

        $result = $this->assembler->create($reflectedTrait);

        self::assertSame('MyTrait', $result->getName());
        self::assertSame('\My\Space', $result->getNamespace());
        self::assertSame($fqsen, $result->getFullyQualifiedStructuralElementName());
    }

    /** @covers ::create */
    public function testTraitWillHaveAnAssociatedLocationInAFile(): void
    {
        $startLocation = new Location(10);
        $endLocation = new Location(15);

        $reflectedTrait = new Trait_(
            new Fqsen('\My\Space\MyTrait'),
            null,
            $startLocation,
            $endLocation,
        );

        $result = $this->assembler->create($reflectedTrait);

        self::assertSame($startLocation, $result->getStartLocation());
        self::assertSame($endLocation, $result->getEndLocation());
    }

    /**
     * @uses \phpDocumentor\Descriptor\Collection
     * @uses \phpDocumentor\Descriptor\ConstantDescriptor
     *
     * @covers ::create
     */
    public function testTraitWillHaveConstants(): void
    {
        $reflectedTrait = new Trait_(new Fqsen('\My\Space\MyTrait'));
        $reflectedConstant = $this->givenReflectedConstantInTrait('CONSTANT', $reflectedTrait);

        $method = $this->whenDescriptorBuilderReturnsConstantBasedOn($reflectedConstant);

        $result = $this->assembler->create($reflectedTrait);

        self::assertSame($method, $result->getConstants()->fetch($reflectedConstant->getName(), false));
    }

    /**
     * @uses \phpDocumentor\Descriptor\Collection
     * @uses \phpDocumentor\Descriptor\PropertyDescriptor
     *
     * @covers ::create
     */
    public function testTraitWillHaveProperties(): void
    {
        $reflectedTrait = new Trait_(new Fqsen('\My\Space\MyTrait'));
        $reflectedProperty = $this->givenReflectedPropertyInTrait('PROPERTY', $reflectedTrait);

        $method = $this->whenDescriptorBuilderReturnsPropertyBasedOn($reflectedProperty);

        $result = $this->assembler->create($reflectedTrait);

        self::assertSame($method, $result->getProperties()->fetch($reflectedProperty->getName(), false));
    }

    /**
     * @uses \phpDocumentor\Descriptor\Collection
     * @uses \phpDocumentor\Descriptor\MethodDescriptor
     *
     * @covers ::create
     */
    public function testTraitWillHaveMethods(): void
    {
        $reflectedTrait = new Trait_(new Fqsen('\My\Space\MyTrait'));
        $reflectedMethod = $this->givenReflectedMethodInTrait('method', $reflectedTrait);

        $method = $this->whenDescriptorBuilderReturnsMethodBasedOn($reflectedMethod);

        $result = $this->assembler->create($reflectedTrait);

        self::assertSame($method, $result->getMethods()->fetch($reflectedMethod->getName(), false));
    }

    private function givenReflectedConstantInTrait(string $constantName, Trait_ $reflectedTrait): Constant
    {
        $reflectedConstant = new Constant(
            new Fqsen(sprintf('%s::%s()', (string) $reflectedTrait->getFqsen(), $constantName)),
        );
        $reflectedTrait->addConstant($reflectedConstant);

        return $reflectedConstant;
    }

    private function whenDescriptorBuilderReturnsConstantBasedOn(Constant $reflectedConstant): ConstantDescriptor
    {
        $constant = new ConstantDescriptor();
        $constant->setName($reflectedConstant->getName());
        $this->builder->buildDescriptor($reflectedConstant, ConstantDescriptor::class)
            ->shouldBeCalled()
            ->willReturn($constant);

        return $constant;
    }

    private function givenReflectedPropertyInTrait(string $propertyName, Trait_ $reflectedTrait): Property
    {
        $reflectedProperty = new Property(
            new Fqsen(sprintf('%s::%s()', (string) $reflectedTrait->getFqsen(), $propertyName)),
        );
        $reflectedTrait->addProperty($reflectedProperty);

        return $reflectedProperty;
    }

    private function whenDescriptorBuilderReturnsPropertyBasedOn(Property $reflectedProperty): PropertyDescriptor
    {
        $property = new PropertyDescriptor();
        $property->setName($reflectedProperty->getName());
        $this->builder->buildDescriptor($reflectedProperty, PropertyDescriptor::class)
            ->shouldBeCalled()
            ->willReturn($property);

        return $property;
    }

    private function givenReflectedMethodInTrait(string $methodName, Trait_ $reflectedTrait): Method
    {
        $reflectedMethod = new Method(
            new Fqsen(sprintf('%s::%s()', (string) $reflectedTrait->getFqsen(), $methodName)),
        );
        $reflectedTrait->addMethod($reflectedMethod);

        return $reflectedMethod;
    }

    private function whenDescriptorBuilderReturnsMethodBasedOn(Method $reflectedMethod): MethodDescriptor
    {
        $method = new MethodDescriptor();
        $method->setName($reflectedMethod->getName());
        $this->builder->buildDescriptor($reflectedMethod, MethodDescriptor::class)
            ->shouldBeCalled()
            ->willReturn($method);

        return $method;
    }
}
