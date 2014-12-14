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
 * Tests the functionality for the ArgumentDescriptor class.
 */
class ArgumentDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArgumentDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ArgumentDescriptor();
    }

    /**
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::getTypes
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::setTypes
     */
    public function testSetAndGetTypes()
    {
        $this->assertSame(array(), $this->fixture->getTypes());

        $this->fixture->setTypes(array(1));

        $this->assertSame(array(1), $this->fixture->getTypes());
    }

    /**
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::getDefault
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::setDefault
     */
    public function testSetAndGetDefault()
    {
        $this->assertSame(null, $this->fixture->getDefault());

        $this->fixture->setDefault('a');

        $this->assertSame('a', $this->fixture->getDefault());
    }

    /**
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::isByReference
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::setByReference
     */
    public function testSetAndGetWhetherArgumentIsPassedByReference()
    {
        $this->assertSame(false, $this->fixture->isByReference());

        $this->fixture->setByReference(true);

        $this->assertSame(true, $this->fixture->isByReference());
    }

    /**
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::getDescription
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
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::getDescription
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
     * @covers phpDocumentor\Descriptor\ArgumentDescriptor::getTypes
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
     * @param string $argumentName The name of the current method.
     *
     * @return ArgumentDescriptor
     */
    private function whenFixtureHasMethodAndArgumentInParentClassWithSameName($argumentName)
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
