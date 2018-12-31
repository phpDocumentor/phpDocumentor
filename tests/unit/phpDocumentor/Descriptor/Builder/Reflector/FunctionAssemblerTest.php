<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @author    Sven Hagemann <sven@rednose.nl>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use Mockery as m;
use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Types\Mixed_;

class FunctionAssemblerTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var FunctionAssembler $fixture */
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
        $this->builderMock->shouldReceive('buildDescriptor')->andReturnUsing(
            function ($value) {
                switch (get_class($value)) {
                    case DocBlock\Tags\Generic::class && $value->getName() === 'package':
                        return new PackageDescriptor();
                    default:
                        throw new \InvalidArgumentException('didn\'t expect ' . get_class($value));
                }
            }
        );
        $this->argumentAssemblerMock = m::mock('phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler');
        $this->argumentAssemblerMock->shouldReceive('getBuilder')->andReturnNull();
        $this->argumentAssemblerMock->shouldReceive('setBuilder')->with($this->builderMock);
        $this->fixture = new FunctionAssembler($this->argumentAssemblerMock);
        $this->fixture->setBuilder($this->builderMock);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler::__construct
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler::mapReflectorPropertiesOntoDescriptor
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler::addArgumentsToFunctionDescriptor
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler::createArgumentDescriptor
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler::addArgumentDescriptorToFunction
     */
    public function testCreateFunctionDescriptorFromReflector()
    {
        // Arrange
        $namespace = 'Namespace';
        $functionName = 'goodbyeWorld';
        $argumentName = 'waveHand';

        $argument = $this->givenAnArgumentWithName($argumentName);
        $functionReflectorMock = $this->givenAFunctionReflector(
            $namespace,
            $functionName,
            $argument,
            $this->givenADocBlockObject()
        );
        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setName($argumentName);

        $this->argumentAssemblerMock->shouldReceive('create')->andReturn($argumentDescriptor);

        // Act
        $descriptor = $this->fixture->create($functionReflectorMock);

        // Assert
        $expectedFqsen = '\\' . $namespace . '\\' . $functionName . '()';
        $this->assertSame($expectedFqsen, (string) $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($functionName, $descriptor->getName());
        $this->assertSame('\\' . $namespace, $descriptor->getNamespace());

        $argument = $descriptor->getArguments()->get($argumentName);
        $this->assertSame($argumentDescriptor, $argument);
    }

    /**
     * Creates a sample function reflector for the tests with the given data.
     *
     * @param string $namespace
     * @param string $functionName
     * @param Argument $argumentMock
     * @param DocBlock|m\MockInterface $docBlockMock
     *
     * @return Function_
     */
    protected function givenAFunctionReflector($namespace, $functionName, $argumentMock, $docBlockMock)
    {
        $functionReflectorMock = new Function_(
            new Fqsen('\\' . $namespace . '\\' . $functionName . '()'),
            $docBlockMock
        );

        $functionReflectorMock->addArgument($argumentMock);

        return $functionReflectorMock;
    }

    /**
     * Generates a DocBlock object with applicable defaults for these tests.
     *
     * @return DocBlock
     */
    protected function givenADocBlockObject()
    {
        $docBlockDescription = new DocBlock\Description('This is an example description');
        return new DocBlock(
            'This is a example description',
            $docBlockDescription,
            [
                new DocBlock\Tags\Generic('package', new DocBlock\Description('PackageName')),
            ]
        );
    }

    /**
     * Prepares a mock Argument with the given name.
     *
     * @param string $argumentName
     *
     * @return Argument
     */
    protected function givenAnArgumentWithName($argumentName)
    {
        return new Argument($argumentName);
    }
}
