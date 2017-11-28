<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\ClassReflector\MethodReflector;
use Mockery as m;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Types\String_;

class MethodAssemblerTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
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
        $this->argumentAssemblerMock->shouldReceive('getBuilder')->once()->andReturn(null);
        $this->argumentAssemblerMock->shouldReceive('setBuilder')->once();

        $this->fixture = new MethodAssembler($this->argumentAssemblerMock);
        $this->fixture->setBuilder($this->builderMock);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::__construct
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::mapReflectorToDescriptor
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addArguments
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addArgument
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addVariadicArgument
     */
    public function testCreateMethodDescriptorFromReflector()
    {
        // Arrange
        $namespace    = 'Namespace';
        $methodName   = 'goodbyeWorld';
        $argumentName = 'variableName';

        $argument = $this->givenAnArgumentWithName($argumentName);
        $methodReflectorMock = $this->givenAMethodReflector(
            $namespace,
            $methodName,
            $argument,
            $this->givenADocBlockObject(true)
        );

        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setName($argumentName);

        $this->argumentAssemblerMock->shouldReceive('create')->andReturn($argumentDescriptor);

        // Act
        $descriptor = $this->fixture->create($methodReflectorMock);

        // Assert
        $expectedFqsen = '\\' . $namespace . '::' . $methodName . '()';
        $this->assertSame($expectedFqsen, (string)$descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($methodName, $descriptor->getName());
        $this->assertSame('\\' . $namespace, $descriptor->getNamespace());
        $this->assertSame('protected', (string)$descriptor->getVisibility());
        $this->assertFalse($descriptor->isFinal());
        $this->assertFalse($descriptor->isAbstract());
        $this->assertFalse($descriptor->isStatic());

        $argument = $descriptor->getArguments()->get($argumentName);
        $this->assertSame($argument->getName(), $argumentDescriptor->getName());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::__construct
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::mapReflectorToDescriptor
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addArguments
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addArgument
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addVariadicArgument
     */
    public function testCreateMethodDescriptorFromReflectorWhenDocblockIsAbsent()
    {
        // Arrange
        $namespace    = 'Namespace';
        $methodName   = 'goodbyeWorld';
        $argumentName = 'waveHand';

        $argumentDescriptorMock = $this->givenAnArgumentWithName($argumentName);
        $methodReflectorMock = $this->givenAMethodReflector(
            $namespace,
            $methodName,
            $argumentDescriptorMock
        );

        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setName($argumentName);

        $this->argumentAssemblerMock->shouldReceive('create')->andReturn($argumentDescriptor);

        // Act
        $descriptor = $this->fixture->create($methodReflectorMock);

        // Assert
        $argument = $descriptor->getArguments()->get($argumentName);
        $this->assertSame($argumentDescriptor, $argument);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::__construct
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::mapReflectorToDescriptor
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addArguments
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addArgument
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addVariadicArgument
     */
    public function testCreateMethodDescriptorFromReflectorWhenParamTagsAreAbsent()
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
            $this->givenADocBlockObject(false)
        );

        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setName($argumentName);

        $this->argumentAssemblerMock->shouldReceive('create')->andReturn($argumentDescriptor);


        // Act
        $descriptor = $this->fixture->create($methodReflectorMock);

        // Assert
        $argument = $descriptor->getArguments()->get($argumentName);
        $this->assertSame($argumentDescriptor, $argument);
    }

    /**
     * Creates a sample method reflector for the tests with the given data.
     *
     * @param string $namespace
     * @param string $methodName
     * @param Argument $argumentMock
     * @param DocBlock|m\MockInterface $docBlockMock
     * @return Method
     */
    protected function givenAMethodReflector($namespace, $methodName, Argument $argumentMock, $docBlockMock = null)
    {
        $method = new Method(
            new Fqsen('\\' . $namespace . '::' . $methodName . '()'),
            new Visibility(Visibility::PROTECTED_),
            $docBlockMock
        );

        $method->addArgument($argumentMock);

        return $method;
    }

    /**
     * Generates a DocBlock object with applicable defaults for these tests.
     *
     * @return DocBlock|m\MockInterface
     */
    protected function givenADocBlockObject($withTags)
    {
        $docBlockDescription = new DocBlock\Description('This is an example description');

        $tags = [];

        if ($withTags) {
            $tags[] = new DocBlock\Tags\Param(
                'variableName',
                new String_(),
                true,
                new DocBlock\Description('foo')
            );
        }

        return new DocBlock('This is a example description', $docBlockDescription, $tags);
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
