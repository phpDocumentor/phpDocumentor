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

namespace phpDocumentor\Compiler\Linker;

use Mockery as m;

/**
 * Tests the functionality for the Linker class.
 */
class LinkerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Compiler\Linker\Linker::setObjectAliasesList
     * @covers phpDocumentor\Compiler\Linker\Linker::findAlias
     */
    public function testFindObjectAliasWithFqsen()
    {
        $object = new \stdClass();
        $fqsen  = '\phpDocumentor\MyClass';

        $linker = new Linker(array());
        $linker->setObjectAliasesList(array($fqsen => $object));

        $this->assertSame($object, $linker->findAlias($fqsen));
    }

    /**
     * @covers phpDocumentor\Compiler\Linker\Linker::findAlias
     */
    public function testFindObjectAliasReturnsNothingWithUnknownFqsen()
    {
        $linker = new Linker(array());

        $this->assertNull($linker->findAlias('\phpDocumentor\MyClass'));
    }

    /**
     * @covers phpDocumentor\Compiler\Linker\Linker::findFieldValue
     */
    public function testFindFqsenInObject()
    {
        $fieldName = 'field';
        $fqsen     = '\phpDocumentor\MyClass';

        $object = m::mock('stdClass');
        $object->shouldReceive('getField')->andReturn($fqsen);

        $linker = new Linker(array());

        $this->assertSame($fqsen, $linker->findFieldValue($object, $fieldName));
    }

    /**
     * @covers phpDocumentor\Compiler\Linker\Linker::__construct
     * @covers phpDocumentor\Compiler\Linker\Linker::getSubstitutions
     */
    public function testSetFieldsToSubstitute()
    {
        $elementList = array(
            'phpDocumentor\Descriptor\ProjectDescriptor' => 'files',
            'phpDocumentor\Descriptor\FileDescriptor'    => 'classes',
            'phpDocumentor\Descriptor\ClassDescriptor'   => 'parent'
        );
        $linker = new Linker($elementList);

        $this->assertSame($elementList, $linker->getSubstitutions());
    }

    /**
     * @covers phpDocumentor\Compiler\Linker\Linker::__construct
     * @covers phpDocumentor\Compiler\Linker\Linker::substitute
     */
    public function testSubstituteFqsenInObject()
    {
        // initialize parameters
        $result    = new \stdClass();
        $fieldName = 'field';

        list($object, $fqsen) = $this->createMockDescriptorForResult($result);

        // prepare linker
        $linker = new Linker(array($fqsen => array($fieldName)));
        $linker->setObjectAliasesList(array($fqsen => $result));

        // execute test.
        $linker->substitute($object);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     *
     *
     * @param $result
     *
     * @return array
     */
    protected function createMockDescriptorForResult($result = null)
    {
        $object = m::mock('stdClass');
        $fqsen  = get_class($object);
        $object->shouldReceive('getField')->atLeast()->once()->andReturn($fqsen);

        if ($result) {
            $object->shouldReceive('setField')->atLeast()->once()->with($result);
        } else {
            $object->shouldReceive('setField')->never();
        }

        return array($object, $fqsen);
    }

    /**
     * @covers phpDocumentor\Compiler\Linker\Linker::__construct
     * @covers phpDocumentor\Compiler\Linker\Linker::substitute
     * @depends testSubstituteFqsenInObject
     */
    public function testMultipleSubstitutionsInOneObject()
    {
        // initialize parameters
        $result     = new \stdClass();
        $fieldNames  = array('field1', 'field2');

        // assert that the getField is called (and returns a FQSEN) and the setField is called with the expected object
        $object = m::mock('stdClass');
        $fqsen  = get_class($object);
        foreach (array_keys($fieldNames) as $index) {
            $object->shouldReceive('getField' . ($index + 1))->atLeast()->once()->andReturn($fqsen);
            $object->shouldReceive('setField' . ($index + 1))->atLeast()->once()->with($result);
        }

        // prepare linker
        $linker = new Linker(array($fqsen => $fieldNames));
        $linker->setObjectAliasesList(array($fqsen => $result));

        // execute test.
        $linker->substitute($object);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Compiler\Linker\Linker::substitute
     * @depends testSubstituteFqsenInObject
     */
    public function testSubstituteFieldsViaChildObject()
    {
        // initialize parameters
        $result         = new \stdClass();
        $childFieldName = 'field';
        $fieldName      = 'child';

        list($childObject, $childFqsen) = $this->createMockDescriptorForResult($result);

        $object = m::mock('stdClass');
        $fqsen  = get_class($object);
        $object->shouldReceive('getChild')->atLeast()->once()->andReturn($childObject);
        $object->shouldReceive('setChild')->never();

        // prepare linker
        $linker = new Linker(
            array(
                 $fqsen => array($fieldName),
                 $childFqsen => array($childFieldName)
            )
        );
        $linker->setObjectAliasesList(array($childFqsen => $result));

        // execute test.
        $linker->substitute($object);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Compiler\Linker\Linker::substitute
     * @depends testSubstituteFqsenInObject
     */
    public function testSubstituteFieldsViaArrayOfChildObjects()
    {
        // initialize parameters
        $result         = new \stdClass();
        $childFieldName = 'field';
        $fieldName      = 'child';

        list($childObject, $childFqsen) = $this->createMockDescriptorForResult($result);

        $object = m::mock('stdClass');
        $fqsen  = get_class($object);
        $object->shouldReceive('getChild')->atLeast()->once()->andReturn(array($childObject));
        $object->shouldReceive('setChild');

        // prepare linker
        $linker = new Linker(
            array(
                 $fqsen => array($fieldName),
                 $childFqsen => array($childFieldName)
            )
        );
        $linker->setObjectAliasesList(array($childFqsen => $result));

        // execute test.
        $linker->substitute($object);

        // mark test as successful due to asserts in Mockery
        $this->assertTrue(true);
    }
}
