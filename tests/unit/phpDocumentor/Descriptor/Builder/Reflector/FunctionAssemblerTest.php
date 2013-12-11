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
class FunctionAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FunctionAssembler $fixture */
    protected $fixture;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->fixture = new FunctionAssembler();
    }

    /**
     * Creates a Descriptor from a provided class.
     *
     * @return void
     */
    public function testCreateFunctionDescriptorFromReflector()
    {
        $namespace = 'Namespace';
        $functionName = 'goodbyeWorld';
        $argumentName = 'waveHand';
        $argumentType = 'boolean';

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

        $argumentMock = m::mock('phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler');
        $argumentMock->shouldReceive('getName')->andReturn($argumentName);
        $argumentMock->shouldReceive('getType')->andReturn($argumentType);
        $argumentMock->shouldReceive('getDefault')->andReturn(true);
        $argumentMock->shouldReceive('isByRef')->andReturn(false);

        $functionReflectorMock = m::mock('phpDocumentor\Reflection\FunctionReflector');
        $functionReflectorMock->shouldReceive('getName')->andReturn($namespace . '\\' . $functionName);
        $functionReflectorMock->shouldReceive('getShortName')->andReturn($functionName);
        $functionReflectorMock->shouldReceive('getNamespace')->andReturn($namespace);
        $functionReflectorMock->shouldReceive('getDocBlock')->andReturn($docBlockMock);
        $functionReflectorMock->shouldReceive('getLinenumber')->andReturn(128);
        $functionReflectorMock->shouldReceive('getArguments')->andReturn(array($argumentMock));

        $descriptor = $this->fixture->create($functionReflectorMock);
        $argument = $descriptor->getArguments()->get($argumentName);

        $this->assertSame($namespace . '\\' . $functionName . '()', $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($functionName, $descriptor->getName());
        $this->assertSame($argumentName, $argument->getName());
        $this->assertSame($argumentType, current($argument->getTypes()));
    }
}
