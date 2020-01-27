<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\Types\String_;

class VarAssemblerTest extends MockeryTestCase
{
    /** @var VarAssembler */
    private $fixture;

    /** @var m\MockInterface|ProjectDescriptorBuilder */
    private $builder;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp() : void
    {
        $this->builder = m::mock(ProjectDescriptorBuilder::class);
        $this->fixture = new VarAssembler();
        $this->fixture->setBuilder($this->builder);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\VarAssembler::create
     */
    public function testCreatingVarDescriptorFromReflector() : void
    {
        $reflector = new Var_('$myParameter', new String_(), new Description('This is a description'));

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('var', $descriptor->getName());
        $this->assertSame('This is a description', (string) $descriptor->getDescription());
        $this->assertSame('$myParameter', $descriptor->getVariableName());
        $this->assertEquals(new String_(), $descriptor->getType());
    }
}
