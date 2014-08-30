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
        $this->fixture->setBuilder($this->getProjectDescriptorBuilderMock());
    }

    /**
     * Creates a Descriptor from a provided class.
     *
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler::create
     *
     * @return void
     */
    public function testCreateClassDescriptorFromReflector()
    {
        $name = 'ClassName';
        $namespace = 'Namespace';
        $docBlockDescriptionContent = <<<DOCBLOCK
/**
 * This is a example description
 */
DOCBLOCK;

        $classReflectorMock = $this->getClassReflectorDescriptor();

        $descriptor = $this->fixture->create($classReflectorMock);

        $this->assertSame($namespace . '\\' . $name, $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame((string) $descriptor->getDescription(), $docBlockDescriptionContent);
    }

    /**
     * Create a ClassReflector mock
     *
     * @return MockInterface
     */
    protected function getClassReflectorDescriptor()
    {
        $name = 'ClassName';
        $namespace = 'Namespace';
        $docBlockDescriptionContent = <<<DOCBLOCK
/**
 * This is a example description
 */
DOCBLOCK;
        $docBlockDescription = new DocBlock\Description($docBlockDescriptionContent);

        $docBlockMock = m::mock('phpDocumentor\Reflection\DocBlock');
        $docBlockMock->shouldReceive('getTagsByName')->andReturn(array());
        $docBlockMock->shouldReceive('getTags')->andReturn(array());
        $docBlockMock->shouldReceive('getShortDescription')->andReturn('This is a example description');
        $docBlockMock->shouldReceive('getLongDescription')->andReturn($docBlockDescription);

        $classReflectorMock = m::mock('phpDocumentor\Reflection\ClassReflector');
        $classReflectorMock->shouldReceive('getName')->once()->andReturn($namespace . '\\' . $name);
        $classReflectorMock->shouldReceive('getShortName')->once()->andReturn($name);
        $classReflectorMock->shouldReceive('getDocBlock')->atLeast()->once()->andReturn($docBlockMock);
        $classReflectorMock->shouldReceive('getLinenumber')->once()->andReturn(1);
        $classReflectorMock->shouldReceive('getParentClass')->once()->andReturn('');
        $classReflectorMock->shouldReceive('isAbstract')->once()->andReturn(false);
        $classReflectorMock->shouldReceive('isFinal')->once()->andReturn(false);
        $classReflectorMock->shouldReceive('getNamespace')->atLeast()->once()->andReturn($namespace);
        $classReflectorMock->shouldReceive('getInterfaces')->atLeast()->once()->andReturn(array('TestInterface'));
        $classReflectorMock->shouldReceive('getConstants')->once()->andReturn(array('Constant'));
        $classReflectorMock->shouldReceive('getProperties')->once()->andReturn(array('Properties'));
        $classReflectorMock->shouldReceive('getMethods')->once()->andReturn(array('Method'));
        $classReflectorMock->shouldReceive('getTraits')->once()->andReturn(array());

        return $classReflectorMock;
    }

    /**
     * Create a descriptor builder mock
     *
     * @return m\MockInterface
     */
    protected function getProjectDescriptorBuilderMock()
    {
        $projectDescriptorBuilderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');

        $projectDescriptorBuilderMock->shouldReceive('buildDescriptor')->andReturnUsing(function ($param) {
            $mock = null;

            switch ($param) {
                case 'Properties':
                    $mock = m::mock('phpDocumentor\Descriptor\PropertiesDescriptor');
                    $mock->shouldReceive('getName')->once()->andReturn('Mock');
                    $mock->shouldReceive('setParent')->once()->andReturn();
                    break;

                case 'Method':
                    $mock = m::mock('phpDocumentor\Descriptor\MethodDescriptor');
                    $mock->shouldReceive('getName')->once()->andReturn('Mock');
                    $mock->shouldReceive('setParent')->once()->andReturn();
                    break;

                case 'Constant':
                    $mock = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');
                    $mock->shouldReceive('getName')->once()->andReturn('Mock');
                    $mock->shouldReceive('setParent')->once()->andReturn();
                    break;
            }

            return $mock;
        });

        return $projectDescriptorBuilderMock;
    }

}
