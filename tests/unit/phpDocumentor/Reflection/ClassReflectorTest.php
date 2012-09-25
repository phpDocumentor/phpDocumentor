<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author     Erik Baars <baarserik@hotmail.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
namespace phpDocumentor\Reflection;

/**
 * Class for testing PHPParser_Node_Stmt.
 *
 * Extends the PHPParser_Node_Stmt so properties and abstract methods can be mocked, and therefor tested.
 */
class NodeMock2 extends \PHPParser_Node_Stmt
{
    public $type = null;

    public $implements = array();

    public $extends = null;
}

/**
 * Class for testing ClassReflector.
 *
 * Extends the ClassReflector so properties and abstract methods can be mocked, and therefor tested.
 */
class ClassReflectorMock extends ClassReflector
{
    public function setTraits(array $v)
    {
        $this->traits = $v;
    }
}

/**
 * Class for testing ClassReflector.
 */
class ClassReflectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the parseSubElements method
     *
     * @covers phpDocumentor\Reflection\ClassReflector::parseSubElements
     *
     * @return void
     */
    public function testParseSubElements()
    {
        $this->markTestIncomplete();
    }

    /**
     * Tests the parseSubElements method
     *
     * @covers phpDocumentor\Reflection\ClassReflector::isAbstract
     *
     * @return void
     */
    public function testIsAbstract()
    {
        //$this->markTestSkipped();
        $node = new NodeMock2();
        $class_reflector = new ClassReflector($node);

        $this->assertFalse($class_reflector->isAbstract());

        $node->type = \PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT;
        $this->assertTrue($class_reflector->isAbstract());
    }

    /**
     * Tests the parseSubElements method
     *
     * @covers phpDocumentor\Reflection\ClassReflector::isFinal
     *
     * @return void
     */
    public function testIsFinal()
    {
        $node = new NodeMock2();
        $class_reflector = new ClassReflector($node);

        $this->assertFalse($class_reflector->isFinal());

        $node->type = \PHPParser_Node_Stmt_Class::MODIFIER_FINAL;
        $this->assertTrue($class_reflector->isFinal());
    }

    /**
     * Tests the parseSubElements method
     *
     * @covers phpDocumentor\Reflection\ClassReflector::getTraits
     *
     * @return void
     */
    public function testGetTraits()
    {
        $node = new NodeMock();
        $class_reflector = new ClassReflectorMock($node);

        $traits = $class_reflector->getTraits();
        $this->assertInternalType('array', $traits);
        $this->assertEmpty($traits);

        $class_reflector->setTraits(array('trait1', 'trait2'));
        $traits = $class_reflector->getTraits();

        $this->assertCount(2, $traits);
        $this->assertEquals('trait1', reset($traits));
    }

    /**
     * Tests the parseSubElements method
     *
     * @covers phpDocumentor\Reflection\ClassReflector::getParentClass
     *
     * @return void
     */
    public function testGetParentClass()
    {
        $node = new NodeMock();
        $class_reflector = new ClassReflectorMock($node);

        $this->assertEquals('', $class_reflector->getParentClass());

        $node->extends = 'dummy';

        $this->assertEquals('\dummy', $class_reflector->getParentClass());
    }

    /**
     * Tests the parseSubElements method
     *
     * @covers phpDocumentor\Reflection\ClassReflector::getInterfaces
     *
     * @return void
     */
    public function testGetInterfaces()
    {
        $node = new NodeMock();
        $class_reflector = new ClassReflectorMock($node);

        $this->assertEquals(array(), $class_reflector->getInterfaces());

        $node->implements = array('dummy');

        $this->assertEquals(array('\dummy'), $class_reflector->getInterfaces());
    }
}
