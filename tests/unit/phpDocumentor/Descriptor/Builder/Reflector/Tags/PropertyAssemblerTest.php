<?php

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tag\PropertyTag;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;
use phpDocumentor\Reflection\Types\String_;

class PropertyAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var PropertyAssembler */
    private $fixture;

    /** @var m\MockInterface|ProjectDescriptorBuilder */
    private $builder;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->builder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new PropertyAssembler();
        $this->fixture->setBuilder($this->builder);
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\PropertyAssembler::create
     */
    public function testCreatingPropertyDescriptorFromReflector()
    {
        $reflector = new Property('$myProperty', new String_(), new Description('This is a description'));

        $descriptor = $this->fixture->create($reflector);

        $this->assertSame('property', $descriptor->getName());
        $this->assertSame('This is a description', (string)$descriptor->getDescription());
        $this->assertSame('$myProperty', $descriptor->getVariableName());
        $this->assertEquals(new String_(), $descriptor->getTypes());
    }
}
