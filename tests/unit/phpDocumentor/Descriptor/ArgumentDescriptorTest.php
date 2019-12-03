<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\ArgumentDescriptor
 */
class ArgumentDescriptorTest extends MockeryTestCase
{
    /** @var ArgumentDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->markTestIncomplete('Review this whole testcase; it is too complicated to change');
        $this->fixture = new ArgumentDescriptor();
    }

    /**
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetTypes() : void
    {
        $this->assertSame(null, $this->fixture->getType());

        $type = new Integer();
        $this->fixture->setType($type);

        $this->assertSame($type, $this->fixture->getType());
    }

    /**
     * @covers ::getMethod
     * @covers ::setMethod
     */
    public function testSetAndGetMethod() : void
    {
        $this->assertSame(null, $this->fixture->getMethod());

        $method = new MethodDescriptor();
        $this->fixture->setMethod($method);

        $this->assertSame($method, $this->fixture->getMethod());
    }

    /**
     * @covers ::getDefault
     * @covers ::setDefault
     */
    public function testSetAndGetDefault() : void
    {
        $this->assertNull($this->fixture->getDefault());

        $this->fixture->setDefault('a');

        $this->assertSame('a', $this->fixture->getDefault());
    }

    /**
     * @covers ::isByReference
     * @covers ::setByReference
     */
    public function testSetAndGetWhetherArgumentIsPassedByReference() : void
    {
        $this->assertFalse($this->fixture->isByReference());

        $this->fixture->setByReference(true);

        $this->assertTrue($this->fixture->isByReference());
    }

    /**
     * @covers ::isVariadic
     * @covers ::setVariadic
     */
    public function testSetAndGetWhetherArgumentIsAVariadic() : void
    {
        $this->assertFalse($this->fixture->isVariadic());

        $this->fixture->setVariadic(true);

        $this->assertTrue($this->fixture->isVariadic());
    }

    /**
     * @covers ::getDescription
     */
    public function testDescriptionInheritsWhenNoneIsPresent() : void
    {
        // Arrange
        $description = 'This is a description';
        $this->fixture->setDescription('');
        $parentArgument = $this->whenFixtureHasMethodAndArgumentInParentClassWithSameName('same_argument');
        $parentArgument->setDescription($description);

        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame($description, $result);
    }

    /**
     * @covers ::getDescription
     */
    public function testDescriptionIsNotInheritedWhenPresent() : void
    {
        // Arrange
        $description = 'This is a description';
        $this->fixture->setDescription($description);
        $parentArgument = $this->whenFixtureHasMethodAndArgumentInParentClassWithSameName('same_argument');
        $parentArgument->setDescription('some random text');
        // Act
        $result = $this->fixture->getDescription();

        // Assert
        $this->assertSame($description, $result);
    }

    /**
     * @covers ::getType
     */
    public function testTypeIsInheritedWhenNoneIsPresent() : void
    {
        // Arrange
        $types = new String_();
        $this->fixture->setType(null);
        $parentArgument = $this->whenFixtureHasMethodAndArgumentInParentClassWithSameName('same_argument');
        $parentArgument->setType($types);
        // Act
        $result = $this->fixture->getType();

        // Assert
        $this->assertSame($types, $result);
    }

    /**
     * @covers ::setMethod
     * @covers ::getInheritedElement
     */
    public function testGetTheArgumentFromWhichThisArgumentInherits() : void
    {
        $this->assertNull(
            $this->fixture->getInheritedElement(),
            'By default, an argument does not have an inherited element'
        );

        $method = new MethodDescriptor();
        $method->setName('same');
        $method->addArgument('abc', $this->fixture);
        $this->fixture->setMethod($method);

        $this->assertNull($this->fixture->getInheritedElement());

        $this->whenFixtureHasMethodAndArgumentInParentClassWithSameName('abcd');

        $this->assertNotNull($this->fixture->getInheritedElement());
    }

    private function whenFixtureHasMethodAndArgumentInParentClassWithSameName(string $argumentName) : ArgumentDescriptor
    {
        $this->fixture->setName($argumentName);

        $parentArgument = new ArgumentDescriptor();
        $parentArgument->setName($argumentName);

        $parentMethod = new MethodDescriptor();
        $parentMethod->setName('same');
        $parentMethod->addArgument($argumentName, $parentArgument);

        $method = new MethodDescriptor();
        $method->setName('same');
        $method->addArgument($argumentName, $this->fixture);
        $this->fixture->setMethod($method);

        $parent = new ClassDescriptor();
        $parent->getMethods()->set('same', $parentMethod);
        $parentMethod->setParent($parent);

        $class = new ClassDescriptor();
        $class->setParent($parent);
        $class->getMethods()->set('same', $method);
        $method->setParent($class);

        return $parentArgument;
    }
}
