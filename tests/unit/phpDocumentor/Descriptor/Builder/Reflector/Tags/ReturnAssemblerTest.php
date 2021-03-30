<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\ApiSetDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ReturnAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ReturnAssembler */
    private $fixture;

    /** @var ApiSetDescriptorBuilder|ObjectProphecy */
    private $builder;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp(): void
    {
        $this->builder = $this->prophesize(ApiSetDescriptorBuilder::class);
        $this->fixture = new ReturnAssembler();
        $this->fixture->setBuilder($this->builder->reveal());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ReturnAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ReturnAssembler::buildDescriptor
     */
    public function testCreatingReturnDescriptorFromReflector(): void
    {
        $reflector = new Return_(new String_(), new Description('This is a description'));

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('return', $descriptor->getName());
        $this->assertSame('This is a description', (string) $descriptor->getDescription());
        $this->assertEquals(new String_(), $descriptor->getType());
    }
}
