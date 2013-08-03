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
 * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler
 */
class ConstantAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ConstantAssembler $fixture */
    protected $fixture;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->fixture = new ConstantAssembler();
    }

    /**
     * Creates a Descriptor from a provided class.
     *
     * @return void
     */
    public function testCreateConstantDescriptorFromReflector()
    {
        $pi = 3.14159265359;
        $name = 'constPI';
        $namespace = 'Namespace';

        $docBlockDescription = new DocBlock\Description('
            /**
             * This is a example description
             */
         ');

        $docBlockMock = m::mock('phpDocumentor\Reflection\DocBlock');
        $docBlockMock->shouldReceive('getTagsByName')->andReturn(array());
        $docBlockMock->shouldReceive('getTags')->andReturn(array());
        $docBlockMock->shouldReceive('getShortDescription')->andReturn('This is a example description');
        $docBlockMock->shouldReceive('getLongDescription')->andReturn($docBlockDescription);

        $constantReflectorMock = m::mock('phpDocumentor\Reflection\ConstantReflector');
        $constantReflectorMock->shouldReceive('getName')->andReturn($namespace . '\\' . $name);
        $constantReflectorMock->shouldReceive('getShortName')->andReturn($name);
        $constantReflectorMock->shouldReceive('getNamespace')->andReturn($namespace);
        $constantReflectorMock->shouldReceive('getDocBlock')->andReturn($docBlockMock);
        $constantReflectorMock->shouldReceive('getValue')->andReturn($pi);
        $constantReflectorMock->shouldReceive('getLinenumber')->andReturn(5);

        $descriptor = $this->fixture->create($constantReflectorMock);

        $this->assertSame($name, $descriptor->getName());
        $this->assertSame('\\' . $namespace . '\\' . $name, $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($pi, $descriptor->getValue());
    }
}
