<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @author    Sven Hagemann <sven@rednose.nl>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Descriptor\Collection;

use Mockery as m;

/**
 * Test class for \phpDocumentor\Descriptor\Builder
 *
 * @covers \phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler
 */
class ClassAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ClassAssembler $fixture */
    protected $fixture;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->fixture = new ClassAssembler();
    }

    /**
     * Creates a Descriptor from a provided class.
     *
     * @return void
     */
    public function testCreateClassDescriptorFromReflector()
    {
        $name = 'ClassName';
        $namespace = 'Namespace';
        $docBlockDescriptionContent = trim('
            /**
             * This is a example description
             */
        ');
        $docBlockDescription = new DocBlock\Description($docBlockDescriptionContent);

        $docBlockMock = m::mock('phpDocumentor\Reflection\DocBlock');
        $docBlockMock->shouldReceive('getTagsByName')->andReturn(array());
        $docBlockMock->shouldReceive('getTags')->andReturn(array());
        $docBlockMock->shouldReceive('getShortDescription')->andReturn('This is a example description');
        $docBlockMock->shouldReceive('getLongDescription')->andReturn($docBlockDescription);

        $classReflectorMock = m::mock('phpDocumentor\Reflection\ClassReflector');
        $classReflectorMock->shouldReceive('getName')->andReturn($namespace . '\\' . $name);
        $classReflectorMock->shouldReceive('getShortName')->andReturn($name);
        $classReflectorMock->shouldReceive('getDocBlock')->andReturn($docBlockMock);
        $classReflectorMock->shouldReceive('getLinenumber')->andReturn(1);
        $classReflectorMock->shouldReceive('getParentClass')->andReturn('');
        $classReflectorMock->shouldReceive('isAbstract')->andReturn(false);
        $classReflectorMock->shouldReceive('isFinal')->andReturn(false);
        $classReflectorMock->shouldReceive('getNamespace')->andReturn($namespace);
        $classReflectorMock->shouldReceive('getInterfaces')->andReturn(array());
        $classReflectorMock->shouldReceive('getConstants')->andReturn(array());
        $classReflectorMock->shouldReceive('getProperties')->andReturn(array());
        $classReflectorMock->shouldReceive('getMethods')->andReturn(array());
        $classReflectorMock->shouldReceive('getTraits')->andReturn(array());

        $descriptor = $this->fixture->create($classReflectorMock);

        $this->assertSame($namespace . '\\' . $name, $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame((string) $descriptor->getDescription(), $docBlockDescriptionContent);
    }
}
