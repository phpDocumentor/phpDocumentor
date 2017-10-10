<?php

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;
use phpDocumentor\Reflection\Types\String_;

class ReturnAssemblerTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ReturnAssembler */
    private $fixture;

    /** @var m\MockInterface|ProjectDescriptorBuilder */
    private $builder;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->builder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new ReturnAssembler();
        $this->fixture->setBuilder($this->builder);
    }

    /**
     * @covers phpDocumentor\Descriptor\Builder\Reflector\Tags\ReturnAssembler::create
     */
    public function testCreatingReturnDescriptorFromReflector()
    {
        $reflector = new Return_(new String_(), new Description('This is a description'));

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('return', $descriptor->getName());
        $this->assertSame('This is a description', (string)$descriptor->getDescription());
        $this->assertEquals(new String_(), $descriptor->getTypes());
    }
}
