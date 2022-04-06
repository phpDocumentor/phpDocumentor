<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument as ProphecyArgument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class MethodAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /** @var MethodAssembler $fixture */
    protected $fixture;

    /** @var ArgumentAssembler|ObjectProphecy */
    protected $argumentAssemblerMock;

    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp(): void
    {
        $this->builderMock = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->builderMock->buildDescriptor(ProphecyArgument::any(), ProphecyArgument::any())->willReturn(null);

        $this->argumentAssemblerMock = $this->prophesize(ArgumentAssembler::class);
        $this->argumentAssemblerMock->getBuilder()->shouldBeCalledOnce()->willReturn(null);
        $this->argumentAssemblerMock->setBuilder(ProphecyArgument::any())->shouldBeCalledOnce();

        $this->fixture = new MethodAssembler($this->argumentAssemblerMock->reveal());
        $this->fixture->setBuilder($this->builderMock->reveal());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::__construct
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::mapReflectorToDescriptor
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addArguments
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addArgument
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler::addVariadicArgument
     */
    public function testCreateMethodDescriptorFromReflector(): void
    {
        // Arrange
        $namespace = 'Namespace';
        $methodName = 'goodbyeWorld';
        $argumentName = 'variableName';

        $argument = $this->givenAnArgumentWithName($argumentName);
        $line = 10;
        $column = 25;
        $endLine = 264;
        $endColumn = 9950;
        $startLocation = $this->givenALocation(10, 25);
        $endLocation = $this->givenALocation(264, 9950);
        $methodReflectorMock = $this->givenAMethodReflector(
            $namespace,
            $methodName,
            $argument,
            $this->givenADocBlockObject(true),
            false,
            $startLocation,
            $endLocation
        );

        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setName($argumentName);

        $this->argumentAssemblerMock
            ->create(ProphecyArgument::any(), ProphecyArgument::any())
            ->shouldBeCalled()
            ->willReturn($argumentDescriptor);

        // Act
        $descriptor = $this->fixture->create($methodReflectorMock);

        // Assert
        $expectedFqsen = '\\' . $namespace . '\\myClass::' . $methodName . '()';
        $this->assertSame($expectedFqsen, (string) $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($methodName, $descriptor->getName());
        $this->assertSame('\\' . $namespace, $descriptor->getNamespace());
        $this->assertSame('protected', $descriptor->getVisibility());
        $this->assertFalse($descriptor->isFinal());
        $this->assertFalse($descriptor->isAbstract());
        $this->assertFalse($descriptor->isStatic());
        $this->assertFalse($descriptor->getHasReturnByReference());
        $this->assertSame($startLocation, $descriptor->getStartLocation());
        $this->assertSame($endLocation, $descriptor->getEndLocation());

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
    public function testCreateMethodDescriptorFromReflectorWhenDocblockIsAbsent(): void
    {
        // Arrange
        $namespace = 'Namespace';
        $methodName = 'goodbyeWorld';
        $argumentName = 'waveHand';

        $argumentDescriptorMock = $this->givenAnArgumentWithName($argumentName);
        $methodReflectorMock = $this->givenAMethodReflector(
            $namespace,
            $methodName,
            $argumentDescriptorMock
        );

        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setName($argumentName);

        $this->argumentAssemblerMock
            ->create(ProphecyArgument::any(), ProphecyArgument::any())
            ->shouldBeCalled()
            ->willReturn($argumentDescriptor);

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
    public function testCreateMethodDescriptorFromReflectorWhenParamTagsAreAbsent(): void
    {
        // Arrange
        $namespace = 'Namespace';
        $methodName = 'goodbyeWorld';
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

        $this->argumentAssemblerMock
            ->create(ProphecyArgument::any(), ProphecyArgument::any())
            ->shouldBeCalled()
            ->willReturn($argumentDescriptor);

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
    public function testCreateMethodDescriptorFromReflectorWithReturnByReference(): void
    {
        // Arrange
        $namespace = 'Namespace';
        $methodName = 'goodbyeWorld';
        $argumentName = 'variableName';

        $argument = $this->givenAnArgumentWithName($argumentName);
        $methodReflectorMock = $this->givenAMethodReflector(
            $namespace,
            $methodName,
            $argument,
            $this->givenADocBlockObject(true),
            true
        );

        $argumentDescriptor = new ArgumentDescriptor();
        $argumentDescriptor->setName($argumentName);

        $this->argumentAssemblerMock
            ->create(ProphecyArgument::any(), ProphecyArgument::any())
            ->shouldBeCalled()
            ->willReturn($argumentDescriptor);

        // Act
        $descriptor = $this->fixture->create($methodReflectorMock);

        // Assert
        $this->assertTrue($descriptor->getHasReturnByReference());
    }

    /**
     * Creates a sample method reflector for the tests with the given data.
     */
    protected function givenAMethodReflector(
        string $namespace,
        string $methodName,
        Argument $argumentMock,
        ?DocBlock $docBlockMock = null,
        bool $hasReturnByReference = false,
        ?Location $location = null,
        ?Location $endLocation = null
    ): Method {
        $method = new Method(
            new Fqsen('\\' . $namespace . '\\myClass::' . $methodName . '()'),
            new Visibility(Visibility::PROTECTED_),
            $docBlockMock,
            false,
            false,
            false,
            $location,
            $endLocation,
            null,
            $hasReturnByReference
        );

        $method->addArgument($argumentMock);

        return $method;
    }

    /**
     * Generates a DocBlock object with applicable defaults for these tests.
     */
    protected function givenADocBlockObject($withTags): DocBlock
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
     */
    protected function givenAnArgumentWithName(string $argumentName): Argument
    {
        return new Argument($argumentName);
    }

    protected function givenALocation(int $line, int $column): Location
    {
        return new Location($line, $column);
    }
}
