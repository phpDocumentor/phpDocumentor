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

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\ArgumentDescriptor
 * @covers ::__construct
 * @covers ::<private>
 */
final class ArgumentDescriptorTest extends TestCase
{
    /**
     * @covers ::setName
     * @covers ::getName
     */
    public function testCanHaveAName(): void
    {
        $argument = new ArgumentDescriptor();
        self::assertSame('', $argument->getName());

        $argument->setName('name');
        self::assertSame('name', $argument->getName());
    }

    /**
     * @covers ::getMethod
     * @covers ::setMethod
     */
    public function testCanBelongToAMethod(): void
    {
        $method = $this->givenAnExampleMethod();

        $argument = new ArgumentDescriptor();
        $argument->setName('abc');
        $this->whenArgumentBelongsToMethod($argument, $method);

        self::assertSame($method, $argument->getMethod());
    }

    /**
     * @covers ::setSummary
     * @covers ::getSummary
     */
    public function testArgumentsCanHaveASummary(): void
    {
        $argument = new ArgumentDescriptor();
        self::assertSame('', $argument->getSummary());

        $argument->setSummary('summary');

        self::assertSame('summary', $argument->getSummary());
    }

    /**
     * @covers ::getSummary
     */
    public function testSummaryInheritsWhenNoneIsPresent(): void
    {
        $parentSummary = 'This is a summary';

        $argument = new ArgumentDescriptor();
        $argument->setName('abc');
        $argument->setSummary('');

        $parentArgument = $this->givenAnArgumentFromWhichItCanInherit($argument);
        $parentArgument->setSummary($parentSummary);

        self::assertSame($parentSummary, $argument->getSummary());
    }

    /**
     * @covers ::setDescription
     * @covers ::getDescription
     */
    public function testArgumentsCanHaveADescription(): void
    {
        $argument = new ArgumentDescriptor();
        $argument->setName('abc');

        self::assertEquals(DescriptionDescriptor::createEmpty(), $argument->getDescription());

        $description = new DescriptionDescriptor(new Description('description'), []);
        $argument->setDescription($description);

        self::assertSame($description, $argument->getDescription());
    }

    /**
     * @covers ::getDescription
     */
    public function testWhenDescriptionIsNullParentDescriptionIsInherited(): void
    {
        $parentDescription = new DescriptionDescriptor(new Description('description'), []);

        $argument = new ArgumentDescriptor();
        $argument->setName('abc');

        $parentArgument = $this->givenAnArgumentFromWhichItCanInherit($argument);
        $parentArgument->setDescription($parentDescription);

        self::assertSame($parentDescription, $argument->getDescription());
    }

    /**
     * @covers ::getDefault
     * @covers ::setDefault
     */
    public function testArgumentsCanHaveADefaultValue(): void
    {
        $argument = new ArgumentDescriptor();
        self::assertNull($argument->getDefault());

        $argument->setDefault('a');

        self::assertSame('a', $argument->getDefault());
    }

    /**
     * @covers ::isByReference
     * @covers ::setByReference
     */
    public function testWhetherAnArgumentCouldBePassedByReference(): void
    {
        $argument = new ArgumentDescriptor();
        self::assertFalse($argument->isByReference());

        $argument->setByReference(true);

        self::assertTrue($argument->isByReference());
    }

    /**
     * @covers ::isVariadic
     * @covers ::setVariadic
     */
    public function testArgumentCanBeAVariadic(): void
    {
        $argument = new ArgumentDescriptor();
        self::assertFalse($argument->isVariadic());

        $argument->setVariadic(true);

        self::assertTrue($argument->isVariadic());
    }

    /**
     * @covers ::getType
     * @covers ::setType
     */
    public function testCanBeAssociatedWithAType(): void
    {
        $argument = new ArgumentDescriptor();
        $argument->setName('abc');

        $method = $this->givenAnExampleMethod();
        $this->whenArgumentBelongsToMethod($argument, $method);
        $this->thenArgumentHasType(null, $argument);

        $type = new Integer();
        $this->whenArgumentIsTypeHintedWith($argument, $type);
        $this->thenArgumentHasType($type, $argument);
    }

    /**
     * @covers ::getType
     */
    public function testTypeIsInheritedWhenNoneIsPresent(): void
    {
        $types = new String_();

        $argument = new ArgumentDescriptor();
        $argument->setName('abc');
        $argument->setType(null);

        $parentArgument = $this->givenAnArgumentFromWhichItCanInherit($argument);
        $parentArgument->setType($types);

        $result = $argument->getType();

        self::assertSame($types, $result);
    }

    /**
     * @covers ::setMethod
     * @covers ::getInheritedElement
     */
    public function testGetTheArgumentFromWhichThisArgumentInherits(): void
    {
        $argument = new ArgumentDescriptor();
        $argument->setName('abc');

        $method = $this->givenAnExampleMethod();
        $this->whenArgumentBelongsToMethod($argument, $method);

        self::assertNull(
            $argument->getInheritedElement(),
            'By default, an argument does not have an inherited element'
        );

        $this->givenAnArgumentFromWhichItCanInherit($argument);

        self::assertNotNull($argument->getInheritedElement());
    }

    /**
     * @return ArgumentDescriptor A 'parent' argument that can be manipulated to test whether inheritance works.
     */
    private function givenAnArgumentFromWhichItCanInherit(ArgumentDescriptor $currentArgument): ArgumentDescriptor
    {
        $argumentName = $currentArgument->getName();

        $parentArgument = new ArgumentDescriptor();
        $parentArgument->setName($argumentName);

        $superClass = $this->givenAClass(new Fqsen('\My\Super\Class'));
        $parentMethod = $this->givenAnExampleMethod();
        $this->whenMethodBelongsToClass($superClass, $parentMethod);
        $this->whenArgumentBelongsToMethod($parentArgument, $parentMethod);

        $class = $this->givenAClass(new Fqsen('\My\Sub\Class'));
        $class->setParent($superClass);
        $method = $this->givenAnExampleMethod($parentMethod->getName());
        $this->whenMethodBelongsToClass($class, $method);
        $this->whenArgumentBelongsToMethod($currentArgument, $method);

        return $parentArgument;
    }

    private function givenAnExampleMethod(string $methodName = 'same'): MethodDescriptor
    {
        $method = new MethodDescriptor();
        $method->setName($methodName);

        return $method;
    }

    private function whenArgumentBelongsToMethod(ArgumentDescriptor $argument, MethodDescriptor $method): void
    {
        $argument->setMethod($method);
        $method->addArgument($argument->getName(), $argument);
    }

    private function thenArgumentHasType(?Type $type, ArgumentDescriptor $argument): void
    {
        self::assertSame($type, $argument->getType());
    }

    private function whenArgumentIsTypeHintedWith(ArgumentDescriptor $argument, Integer $type): void
    {
        $argument->setType($type);
    }

    private function givenAClass(Fqsen $fqsen): ClassDescriptor
    {
        $parent = new ClassDescriptor();
        $parent->setFullyQualifiedStructuralElementName($fqsen);

        return $parent;
    }

    private function whenMethodBelongsToClass(ClassDescriptor $class, MethodDescriptor $method): void
    {
        $class->getMethods()->set($method->getName(), $method);
        $method->setParent($class);
    }
}
