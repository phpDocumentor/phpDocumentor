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

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\ArgumentDescriptor
 */
class ArgumentDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ArgumentDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ArgumentDescriptor();
    }

    /**
     * @covers ::getTypes
     * @covers ::setTypes
     */
    public function testSetAndGetTypes()
    {
        $this->assertSame(array(), $this->fixture->getTypes());

        $this->fixture->setTypes(array(1));

        $this->assertSame(array(1), $this->fixture->getTypes());
    }

    /**
     * @covers ::getDefault
     * @covers ::setDefault
     */
    public function testSetAndGetDefault()
    {
        $this->assertNull($this->fixture->getDefault());

        $this->fixture->setDefault('a');

        $this->assertSame('a', $this->fixture->getDefault());
    }

    /**
     * @covers ::isByReference
     * @covers ::setByReference
     */
    public function testSetAndGetWhetherArgumentIsPassedByReference()
    {
        $this->assertFalse($this->fixture->isByReference());

        $this->fixture->setByReference(true);

        $this->assertTrue($this->fixture->isByReference());
    }


    /**
     * @covers ::isVariadic
     * @covers ::setVariadic
     */
    public function testSetAndGetWhetherArgumentIsAVariadic()
    {
        $this->assertFalse($this->fixture->isVariadic());

        $this->fixture->setVariadic(true);

        $this->assertTrue($this->fixture->isVariadic());
    }

    /**
     * @covers ::getDescription
     */
    public function testDescriptionInheritsWhenNoneIsPresent()
    {
        // Arrange
        $description = 'This is a description';
        $this->fixture->setDescription(null);
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
    public function testDescriptionIsNotInheritedWhenPresent()
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
     * @covers ::getTypes
     */
    public function testTypeIsInheritedWhenNoneIsPresent()
    {
        // Arrange
        $types = array('string');
        $this->fixture->setTypes(null);
        $parentArgument = $this->whenFixtureHasMethodAndArgumentInParentClassWithSameName('same_argument');
        $parentArgument->setTypes($types);
        // Act
        $result = $this->fixture->getTypes();

        // Assert
        $this->assertSame($types, $result);
    }


    /**
     * @covers ::setMethod
     * @covers ::getInheritedElement
     */
    public function testGetTheArgumentFromWhichThisArgumentInherits()
    {
        $this->assertNull(
            $this->fixture->getInheritedElement(),
            'By default, an argument does not have an inherited element'
        );

        $method = new MethodDescriptor;
        $method->setName('same');
        $method->addArgument('abc', $this->fixture);
        $this->fixture->setMethod($method);

        $this->assertNull($this->fixture->getInheritedElement());

        $this->whenFixtureHasMethodAndArgumentInParentClassWithSameName('abcd');

        $this->assertNotNull($this->fixture->getInheritedElement());
    }

    private function whenFixtureHasMethodAndArgumentInParentClassWithSameName(string $argumentName): ArgumentDescriptor
    {
        $this->fixture->setName($argumentName);

        $parentArgument = new ArgumentDescriptor();
        $parentArgument->setName($argumentName);

        $parentMethod = new MethodDescriptor();
        $parentMethod->setName('same');
        $parentMethod->addArgument($argumentName, $parentArgument);

        $method = new MethodDescriptor;
        $method->setName('same');
        $method->addArgument($argumentName, $this->fixture);
        $this->fixture->setMethod($method);

        $parent = new ClassDescriptor();
        $parent->getMethods()->set('same', $parentMethod);
        $parentMethod->setParent($parent);

        $class  = new ClassDescriptor();
        $class->setParent($parent);
        $class->getMethods()->set('same', $method);
        $method->setParent($class);

        return $parentArgument;
    }
}
