<?php

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Types\String_;

class ParamAssemblerTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ParamAssembler */
    private $fixture;

    /** @var m\MockInterface|ProjectDescriptorBuilder */
    private $builder;

    /**
     * Initializes the fixture for this test.
     */
    protected function setUp()
    {
        $this->builder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new ParamAssembler();
        $this->fixture->setBuilder($this->builder);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\ParamAssembler::create
     */
    public function testCreatingParamDescriptorFromReflector()
    {
        $reflector = new Param('$myParameter', new String_(), false, new Description('This is a description'));

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('param', $descriptor->getName());
        $this->assertSame('This is a description', (string) $descriptor->getDescription());
        $this->assertSame('$myParameter', $descriptor->getVariableName());
        $this->assertEquals(new String_(), $descriptor->getType());
    }
}
