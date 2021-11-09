<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Enum_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\EnumAssembler
 */
final class EnumAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /** @var EnumAssembler */
    private $assembler;

    /** @var ProjectDescriptorBuilder&ObjectProphecy */
    private $builder;

    public function setUp(): void
    {
        $this->assembler = new EnumAssembler();
        $this->builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->builder->getDefaultPackage()->willReturn('test');
        $this->assembler->setBuilder($this->builder->reveal());
    }

    /**
     * @covers ::buildDescriptor
     */
    public function testAssembleBasicDescriptor(): void
    {
        $fqsen = new Fqsen('\MyNamespace\Enum');
        $enum = new Enum_(
            $fqsen,
            new String_(),
            new DocBlock('Summary'),
            new Location(10, 0)
        );

        $descriptor = $this->assembler->create($enum);

        self::assertSame('Enum', $descriptor->getName());
        self::assertSame($fqsen, $descriptor->getFullyQualifiedStructuralElementName());
        self::assertSame(10, $descriptor->getLine());
    }
}
