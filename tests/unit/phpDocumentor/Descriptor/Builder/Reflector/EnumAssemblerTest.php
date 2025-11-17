<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\EnumCaseDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Enum_;
use phpDocumentor\Reflection\Php\EnumCase;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/** @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\EnumAssembler */
final class EnumAssemblerTest extends TestCase
{
    use ProphecyTrait;

    private EnumAssembler $assembler;

    /** @var ProjectDescriptorBuilder&ObjectProphecy */
    private $builder;

    public function setUp(): void
    {
        $this->assembler = new EnumAssembler();
        $this->builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->builder->getDefaultPackageName()->willReturn('test');
        $this->assembler->setBuilder($this->builder->reveal());
    }

    public function testAssembleBasicDescriptor(): void
    {
        $fqsen = new Fqsen('\MyNamespace\Enum');
        $enum = $this->givenExampleEnum($fqsen);

        $descriptor = $this->assembler->create($enum);

        self::assertSame('Enum', $descriptor->getName());
        self::assertSame($fqsen, $descriptor->getFullyQualifiedStructuralElementName());
        self::assertSame(10, $descriptor->getLine());
        self::assertCount(0, $descriptor->getCases());
    }

    public function testEnumsCanHaveCases(): void
    {
        $caseFqsen = new Fqsen('\MyNamespace\Enum\Case');
        $case = new EnumCase($caseFqsen, new DocBlock('Summary'), null, null, new Expression('Hearts'));

        $fqsen = new Fqsen('\MyNamespace\Enum');
        $enum = $this->givenExampleEnum($fqsen);
        $enum->addCase($case);

        $this->builder
            ->buildDescriptor(Argument::any(), EnumCaseDescriptor::class)
            // @phpcs:ignore SlevomatCodingStandard.Functions.StaticClosure.ClosureNotStatic
            ->will(function ($value) {
                $enumCaseDescriptor = new EnumCaseDescriptor();
                $enumCaseDescriptor->setName($value[0]->getName());
                $enumCaseDescriptor->setValue($value[0]->getValue(false));

                return $enumCaseDescriptor;
            });

        $descriptor = $this->assembler->create($enum);

        self::assertCount(1, $descriptor->getCases());
        self::assertSame($case->getName(), $descriptor->getCases()[$case->getName()]->getName());
        self::assertSame($case->getValue(false), $descriptor->getCases()[$case->getName()]->getValue());
        self::assertSame($descriptor, $descriptor->getCases()[$case->getName()]->getParent());
    }

    private function givenExampleEnum(Fqsen $fqsen): Enum_
    {
        return new Enum_(
            $fqsen,
            new String_(),
            new DocBlock('Summary'),
            new Location(10, 0),
        );
    }
}
