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

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;

/**
 * Test class for phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler
 */
class ArgumentAssemblerTest extends MockeryTestCase
{
    /** @var ArgumentAssembler $fixture */
    protected $fixture;

    /** @var ProjectDescriptorBuilder|m\MockInterface */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp() : void
    {
        $this->builderMock = m::mock(ProjectDescriptorBuilder::class);
        $this->fixture = new ArgumentAssembler();
        $this->fixture->setBuilder($this->builderMock);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::create
     */
    public function testCreateArgumentDescriptorFromReflector() : void
    {
        // Arrange
        $name = 'goodArgument';
        $type = new Boolean();

        $argumentReflectorMock = $this->givenAnArgumentReflectorWithNameAndType($name, $type);

        // Act
        $descriptor = $this->fixture->create($argumentReflectorMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($type, $descriptor->getType());
        $this->assertNull($descriptor->getDefault());
        $this->assertFalse($descriptor->isByReference());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::overwriteTypeAndDescriptionFromParamTag
     */
    public function testIfTypeAndDescriptionAreSetFromParamDescriptor() : void
    {
        // Arrange
        $name = 'goodArgument';
        $type = new Boolean();

        $argumentReflectorMock = $this->givenAnArgumentReflectorWithNameAndType($name, $type);

        // Mock a paramDescriptor
        $paramDescriptorTagMock = m::mock(ParamDescriptor::class);
        $paramDescriptorTagMock->shouldReceive('getVariableName')->once()->andReturn($name);
        $paramDescriptorTagMock->shouldReceive('getDescription')->once()->andReturn('Is this a good argument, or nah?');
        $paramDescriptorTagMock->shouldReceive('getType')->once()->andReturn($type);

        // Act
        $descriptor = $this->fixture->create($argumentReflectorMock, [$paramDescriptorTagMock]);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($type, $descriptor->getType());
        $this->assertNull($descriptor->getDefault());
        $this->assertFalse($descriptor->isByReference());
        $this->assertFalse($descriptor->isVariadic());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::create
     */
    public function testIfVariadicArgumentsAreDetected() : void
    {
        // Arrange
        $name = 'goodArgument';
        $type = new Boolean();

        $argumentReflectorMock = $this->givenAnArgumentReflectorWithNameAndType($name, $type, true);

        // Mock a paramDescriptor
        $paramDescriptorTagMock = m::mock(ParamDescriptor::class);
        $paramDescriptorTagMock->shouldReceive('getVariableName')->once()->andReturn($name);
        $paramDescriptorTagMock->shouldReceive('getDescription')->once()->andReturn('Is this a good argument, or nah?');
        $paramDescriptorTagMock->shouldReceive('getType')->once()->andReturn($type);

        // Act
        $descriptor = $this->fixture->create($argumentReflectorMock, [$paramDescriptorTagMock]);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($type, $descriptor->getType());
        $this->assertNull($descriptor->getDefault());
        $this->assertFalse($descriptor->isByReference());
        $this->assertTrue($descriptor->isVariadic());
    }

    protected function givenAnArgumentReflectorWithNameAndType(
        string $name,
        Type $type,
        bool $isVariadic = false
    ) : Argument {
        return new Argument($name, $type, null, false, $isVariadic);
    }
}
