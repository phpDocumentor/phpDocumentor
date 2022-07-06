<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\ApiSetDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class ThrowsAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ThrowsAssembler */
    private $fixture;

    /** @var ApiSetDescriptorBuilder|ObjectProphecy */
    private $builder;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp(): void
    {
        $this->builder = $this->prophesize(ApiSetDescriptorBuilder::class);
        $this->fixture = new ThrowsAssembler();
        $this->fixture->setBuilder($this->builder->reveal());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ThrowsAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ThrowsAssembler::buildDescriptor
     */
    public function testCreatingThrowsDescriptorFromReflector(): void
    {
        $types = new Object_(new Fqsen('\InvalidAgumentException'));
        $reflector = new Throws(
            $types,
            new Description('This is a description')
        );

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('throws', $descriptor->getName());
        $this->assertSame('This is a description', (string) $descriptor->getDescription());
        $this->assertSame($types, $descriptor->getType());
    }
}
