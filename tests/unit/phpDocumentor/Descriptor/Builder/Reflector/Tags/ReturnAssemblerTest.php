<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Types\String_;

class ReturnAssemblerTest extends MockeryTestCase
{
    /** @var ReturnAssembler */
    private $fixture;

    /** @var m\MockInterface|ProjectDescriptorBuilder */
    private $builder;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp() : void
    {
        $this->builder = m::mock(ProjectDescriptorBuilder::class);
        $this->fixture = new ReturnAssembler();
        $this->fixture->setBuilder($this->builder);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ReturnAssembler::create
     */
    public function testCreatingReturnDescriptorFromReflector() : void
    {
        $reflector = new Return_(new String_(), new Description('This is a description'));

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('return', $descriptor->getName());
        $this->assertSame('This is a description', (string) $descriptor->getDescription());
        $this->assertEquals(new String_(), $descriptor->getType());
    }
}
