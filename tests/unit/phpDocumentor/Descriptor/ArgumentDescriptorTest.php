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

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\ArgumentDescriptor
 */
final class ArgumentDescriptorTest extends TestCase
{
    /**
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetTypes(): void
    {
        $fixture = new ArgumentDescriptor();
        self::assertSame(null, $fixture->getType());

        $type = new Integer();
        $fixture->setType($type);

        self::assertSame($type, $fixture->getType());
    }

    /**
     * @covers ::getType
     */
    public function testTypeIsInheritedWhenNoneIsPresent(): void
    {
        $types = new String_();

        $fixture = new ArgumentDescriptor();
        $parentArgument = $this->whenFixtureHasMethodAndArgumentInParentClassWithSameName(
            $fixture,
            'argumentName'
        );
        $fixture->setType(null);
        $parentArgument->setType($types);

        $result = $fixture->getType();

        self::assertSame($types, $result);
    }

    /**
     * @covers ::getMethod
     * @covers ::setMethod
     */
    public function testSetAndGetMethod(): void
    {
        $fixture = new ArgumentDescriptor();
        self::assertSame(null, $fixture->getMethod());

        $method = new MethodDescriptor();
        $fixture->setMethod($method);

        self::assertSame($method, $fixture->getMethod());
    }

    /**
     * @covers ::getDefault
     * @covers ::setDefault
     */
    public function testSetAndGetDefault(): void
    {
        $fixture = new ArgumentDescriptor();
        self::assertNull($fixture->getDefault());

        $fixture->setDefault('a');

        self::assertSame('a', $fixture->getDefault());
    }

    /**
     * @covers ::isByReference
     * @covers ::setByReference
     */
    public function testSetAndGetWhetherArgumentIsPassedByReference(): void
    {
        $fixture = new ArgumentDescriptor();
        self::assertFalse($fixture->isByReference());

        $fixture->setByReference(true);

        self::assertTrue($fixture->isByReference());
    }

    /**
     * @covers ::isVariadic
     * @covers ::setVariadic
     */
    public function testSetAndGetWhetherArgumentIsAVariadic(): void
    {
        $fixture = new ArgumentDescriptor();
        self::assertFalse($fixture->isVariadic());

        $fixture->setVariadic(true);

        self::assertTrue($fixture->isVariadic());
    }

    /**
     * @covers ::setMethod
     * @covers ::getInheritedElement
     */
    public function testGetTheArgumentFromWhichThisArgumentInherits(): void
    {
        $fixture = new ArgumentDescriptor();

        self::assertNull(
            $fixture->getInheritedElement(),
            'By default, an argument does not have an inherited element'
        );

        $method = new MethodDescriptor();
        $method->setName('same');
        $method->addArgument('abc', $fixture);
        $fixture->setMethod($method);

        self::assertNull($fixture->getInheritedElement());

        $this->whenFixtureHasMethodAndArgumentInParentClassWithSameName($fixture, 'abcd');

        self::assertNotNull($fixture->getInheritedElement());
    }

    private function whenFixtureHasMethodAndArgumentInParentClassWithSameName(
        ArgumentDescriptor $fixture,
        string $argumentName
    ): ArgumentDescriptor {
        $fixture->setName($argumentName);

        $parentArgument = new ArgumentDescriptor();
        $parentArgument->setName($argumentName);

        $parentMethod = new MethodDescriptor();
        $parentMethod->setName('same');
        $parentMethod->addArgument($argumentName, $parentArgument);

        $method = new MethodDescriptor();
        $method->setName('same');
        $method->addArgument($argumentName, $fixture);
        $fixture->setMethod($method);

        $parent = new ClassDescriptor();
        $parent->setFullyQualifiedStructuralElementName(new Fqsen('\My\Super\Class'));
        $parent->getMethods()->set('same', $parentMethod);
        $parentMethod->setParent($parent);

        $class = new ClassDescriptor();
        $class->setFullyQualifiedStructuralElementName(new Fqsen('\My\Sub\Class'));
        $class->setParent($parent);
        $class->getMethods()->set('same', $method);
        $method->setParent($class);

        return $parentArgument;
    }
}
