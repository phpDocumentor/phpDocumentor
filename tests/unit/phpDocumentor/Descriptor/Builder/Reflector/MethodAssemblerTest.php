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

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\ClassReflector\MethodReflector;
use Mockery as m;

class MethodAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var MethodAssembler $fixture */
    protected $fixture;

    /** @var ArgumentAssembler|m\MockInterface */
    protected $argumentAssemblerMock;

    /** @var ProjectDescriptorBuilder|m\MockInterface */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->builderMock->shouldReceive('buildDescriptor')->andReturn(null);

        $this->argumentAssemblerMock = m::mock('phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler');
        $this->argumentAssemblerMock->shouldReceive('getBuilder')->andReturn($this->builderMock);

        $this->fixture = new MethodAssembler($this->argumentAssemblerMock);
        $this->fixture->setBuilder($this->builderMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::__construct
     * @covers phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::mapReflectorToDescriptor
     * @covers phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addArguments
     * @covers phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addArgument
     * @covers phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addVariadicArgument
     */
    public function testCreateMethodDescriptorFromReflector()
    {
        // Arrange
        $namespace    = 'Namespace';
        $methodName   = 'goodbyeWorld';
        $argumentName = 'waveHand';

        $argumentDescriptorMock = $this->givenAnArgumentWithName($argumentName);
        $methodReflectorMock = $this->givenAMethodReflector(
            $namespace,
            $methodName,
            $argumentDescriptorMock,
            $this->givenADocBlockObject()
        );

        // Act
        $descriptor = $this->fixture->create($methodReflectorMock);

        // Assert
        $expectedFqsen = $namespace . '\\' . $methodName . '()';
        $this->assertSame($expectedFqsen, $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($methodName, $descriptor->getName());
        $this->assertSame('protected', $descriptor->getVisibility());
        $this->assertSame(false, $descriptor->isFinal());
        $this->assertSame(false, $descriptor->isAbstract());
        $this->assertSame(false, $descriptor->isStatic());

        $argument = $descriptor->getArguments()->get($argumentName);
        $this->assertSame($argument, $argumentDescriptorMock);
    }

    /**
     * Creates a sample method reflector for the tests with the given data.
     *
     * @param string                             $namespace
     * @param string                             $methodName
     * @param ArgumentDescriptor|m\MockInterface $argumentMock
     * @param DocBlock|m\MockInterface           $docBlockMock
     *
     * @return MethodReflector|m\MockInterface
     */
    protected function givenAMethodReflector($namespace, $methodName, $argumentMock, $docBlockMock)
    {
        $methodReflectorMock = m::mock('phpDocumentor\Reflection\MethodReflector');
        $methodReflectorMock->shouldReceive('getName')->andReturn($namespace . '\\' . $methodName);
        $methodReflectorMock->shouldReceive('getShortName')->andReturn($methodName);
        $methodReflectorMock->shouldReceive('getNamespace')->andReturn($namespace);
        $methodReflectorMock->shouldReceive('getDocBlock')->andReturn($docBlockMock);
        $methodReflectorMock->shouldReceive('getLinenumber')->andReturn(128);
        $methodReflectorMock->shouldReceive('getArguments')->andReturn(array($argumentMock));
        $methodReflectorMock->shouldReceive('getVisibility')->andReturn('protected');
        $methodReflectorMock->shouldReceive('isFinal')->andReturn(false);
        $methodReflectorMock->shouldReceive('isAbstract')->andReturn(false);
        $methodReflectorMock->shouldReceive('isStatic')->andReturn(false);

        return $methodReflectorMock;
    }

    /**
     * Generates a DocBlock object with applicable defaults for these tests.
     *
     * @return DocBlock|m\MockInterface
     */
    protected function givenADocBlockObject()
    {
        $docBlockDescription = new DocBlock\Description('This is an example description');

        $docBlockMock = m::mock('phpDocumentor\Reflection\DocBlock');
        $docBlockMock->shouldReceive('getTags')->andReturn(array());
        $docBlockMock->shouldReceive('getShortDescription')->andReturn('This is a example description');
        $docBlockMock->shouldReceive('getLongDescription')->andReturn($docBlockDescription);

        $docBlockMock->shouldReceive('getTagsByName')->andReturnUsing(function ($param) {
            $tag = m::mock('phpDocumentor\Reflection\DocBlock\Tag');

            $tag->shouldReceive('isVariadic')->once()->andReturn(true);
            $tag->shouldReceive('getVariableName')->atLeast()->once()->andReturn('variableName');
            $tag->shouldReceive('getTypes')->once()->andReturn(array('string'));
            $tag->shouldReceive('getDescription')->once()->andReturn('I am a tag');

            return array($tag);
        });

        return $docBlockMock;
    }

    /**
     * Prepares a mock Argument with the given name.
     *
     * @param string $argumentName
     *
     * @return ArgumentDescriptor|m\MockInterface
     */
    protected function givenAnArgumentWithName($argumentName)
    {
        $argumentMock = m::mock('phpDocumentor\Descriptor\ArgumentDescriptor');
        $argumentMock->shouldReceive('getName')->andReturn($argumentName);
        $argumentMock->shouldReceive('setMethod');
        $this->argumentAssemblerMock->shouldReceive('create')->andReturn($argumentMock);

        return $argumentMock;
    }
}
