<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Docblock;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\ApiSetDescriptorBuilder;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class DescriptionAssemblerReducerTest extends TestCase
{
    use ProphecyTrait;

    public function testNullDescriptorReturnsNull(): void
    {
        $reducer = new DescriptionAssemblerReducer();

        self::assertNull($reducer->create(
            new DocBlock('Summary', new DocBlock\Description('template')),
            null
        ));
    }

    public function testCreateSetsDescriptionDescriptor(): void
    {
        $builder = $this->prophesize(ApiSetDescriptorBuilder::class);
        $builder->buildDescriptor(Argument::type(DocBlock\Tag::class), Argument::is(TagDescriptor::class))
            ->willReturn(new TagDescriptor('Tag'));

        $inputData = new DocBlock(
            'Summary',
            new DocBlock\Description('template', [new DocBlock\Tags\Generic('Tag')])
        );

        $reducer = new DescriptionAssemblerReducer();
        $reducer->setBuilder($builder->reveal());
        $descriptor = new ClassDescriptor();

        $result = $reducer->create($inputData, $descriptor);

        self::assertEquals(
            new DescriptionDescriptor($inputData->getDescription(), [new TagDescriptor('Tag')]),
            $result->getDescription()
        );
    }
}
