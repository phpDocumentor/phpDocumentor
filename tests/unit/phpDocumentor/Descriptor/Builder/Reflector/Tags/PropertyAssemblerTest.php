<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class PropertyAssemblerTest extends TestCase
{
    /** @var PropertyAssembler */
    private $fixture;

    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    private $builder;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp() : void
    {
        $this->builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->fixture = new PropertyAssembler();
        $this->fixture->setBuilder($this->builder->reveal());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\PropertyAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\PropertyAssembler::buildDescriptor
     */
    public function testCreatingPropertyDescriptorFromReflector() : void
    {
        $reflector = new Property(
            '$myProperty',
            new String_(),
            new Description('This is a description')
        );

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('property', $descriptor->getName());
        $this->assertSame('This is a description', (string) $descriptor->getDescription());
        $this->assertSame('$myProperty', $descriptor->getVariableName());
        $this->assertEquals(new String_(), $descriptor->getType());
    }
}
