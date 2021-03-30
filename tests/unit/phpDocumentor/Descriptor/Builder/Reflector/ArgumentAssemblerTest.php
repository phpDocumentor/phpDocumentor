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

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\ApiSetDescriptorBuilder;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Boolean;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Test class for phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler
 */
class ArgumentAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ArgumentAssembler $fixture */
    protected $fixture;

    /** @var ApiSetDescriptorBuilder|ObjectProphecy */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp(): void
    {
        $this->builderMock = $this->prophesize(ApiSetDescriptorBuilder::class);
        $this->fixture = new ArgumentAssembler();
        $this->fixture->setBuilder($this->builderMock->reveal());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::create
     */
    public function testCreateArgumentDescriptorFromReflector(): void
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
     * @uses \phpDocumentor\Descriptor\Tag\ParamDescriptor
     *
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::overwriteTypeAndDescriptionFromParamTag
     */
    public function testIfTypeAndDescriptionAreSetFromParamDescriptor(): void
    {
        // Arrange
        $name = 'goodArgument';
        $type = new Boolean();
        $description = new DescriptionDescriptor(new Description('description'), []);

        $argumentReflectorMock = $this->givenAnArgumentReflectorWithNameAndType($name, $type);

        // Mock a paramDescriptor
        $paramDescriptor = new ParamDescriptor('param');
        $paramDescriptor->setVariableName($name);
        $paramDescriptor->setDescription($description);
        $paramDescriptor->setType($type);

        // Act
        $descriptor = $this->fixture->create($argumentReflectorMock, [$paramDescriptor]);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($type, $descriptor->getType());
        $this->assertNull($descriptor->getDefault());
        $this->assertFalse($descriptor->isByReference());
        $this->assertFalse($descriptor->isVariadic());
        $this->assertSame($description, $descriptor->getDescription());
    }

    /**
     * @uses \phpDocumentor\Descriptor\Tag\ParamDescriptor
     *
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler::create
     */
    public function testIfVariadicArgumentsAreDetected(): void
    {
        // Arrange
        $name = 'goodArgument';
        $type = new Boolean();
        $description = new DescriptionDescriptor(new Description('description'), []);

        $argumentReflectorMock = $this->givenAnArgumentReflectorWithNameAndType($name, $type, true);

        // Mock a paramDescriptor
        $paramDescriptor = new ParamDescriptor('param');
        $paramDescriptor->setVariableName($name);
        $paramDescriptor->setDescription($description);
        $paramDescriptor->setType($type);

        // Act
        $descriptor = $this->fixture->create($argumentReflectorMock, [$paramDescriptor]);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($type, $descriptor->getType());
        $this->assertNull($descriptor->getDefault());
        $this->assertFalse($descriptor->isByReference());
        $this->assertTrue($descriptor->isVariadic());
        $this->assertSame($description, $descriptor->getDescription());
    }

    protected function givenAnArgumentReflectorWithNameAndType(
        string $name,
        Type $type,
        bool $isVariadic = false
    ): Argument {
        return new Argument($name, $type, null, false, $isVariadic);
    }
}
