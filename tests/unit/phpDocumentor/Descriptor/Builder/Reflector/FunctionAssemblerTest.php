<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use InvalidArgumentException;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Function_;
use function get_class;

class FunctionAssemblerTest extends MockeryTestCase
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
    protected function setUp() : void
    {
        $this->builderMock = m::mock(ProjectDescriptorBuilder::class);
        $this->builderMock->shouldReceive('buildDescriptor')->andReturnUsing(
            static function ($value) {
                switch (get_class($value)) {
                    case DocBlock\Tags\Generic::class && $value->getName() === 'package':
                        return new PackageDescriptor();
                    default:
                        throw new InvalidArgumentException('didn\'t expect ' . get_class($value));
                }
            }
        );
        $this->argumentAssemblerMock = m::mock(ArgumentAssembler::class);
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
    public function testCreateFunctionDescriptorFromReflector() : void
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
     * @param DocBlock|m\MockInterface $docBlockMock
     */
    protected function givenAFunctionReflector(
        string $namespace,
        string $functionName,
        Argument $argumentMock,
        $docBlockMock
    ) : Function_ {
        $functionReflectorMock = new Function_(
            new Fqsen('\\' . $namespace . '\\' . $functionName . '()'),
            $docBlockMock
        );

        $functionReflectorMock->addArgument($argumentMock);

        return $functionReflectorMock;
    }

    /**
     * Generates a DocBlock object with applicable defaults for these tests.
     */
    protected function givenADocBlockObject() : DocBlock
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
     */
    protected function givenAnArgumentWithName(string $argumentName) : Argument
    {
        return new Argument($argumentName);
    }
}
