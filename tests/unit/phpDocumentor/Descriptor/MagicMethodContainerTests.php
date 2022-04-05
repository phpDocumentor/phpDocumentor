<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use Mockery as m;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Tag\MethodDescriptor as TagMethodDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Types\String_;

trait MagicMethodContainerTests
{
    /**
     * @covers       ::getMagicMethods
     * @dataProvider provideMagicMethodProperties
     */
    public function testGetMagicMethods(bool $isStatic): void
    {
        $methodName = 'methodName';
        $description = new DescriptionDescriptor(new Description('description'), []);
        $response = new ReturnDescriptor('return');
        $response->setType(new String_());
        $argument = m::mock(ArgumentDescriptor::class);
        $argument->shouldReceive('setMethod');

        $methodMock = m::mock(TagMethodDescriptor::class);
        $methodMock->shouldReceive('getMethodName')->andReturn($methodName);
        $methodMock->shouldReceive('getDescription')->andReturn($description);
        $methodMock->shouldReceive('getResponse')->andReturn($response);
        $methodMock->shouldReceive('getArguments')->andReturn(new Collection(['argument1' => $argument]));
        $methodMock->shouldReceive('isStatic')->andReturn($isStatic);
        $methodMock->shouldReceive('getHasReturnByReference')->andReturn(false);

        $this->fixture->getTags()->fetch('method', new Collection())->add($methodMock);

        $magicMethods = $this->fixture->getMagicMethods();

        $this->assertCount(1, $magicMethods);
    }

    /**
     * Provider to test different properties for a class magic method
     * (provides isStatic)
     *
     * @return bool[][]
     */
    public function provideMagicMethodProperties(): array
    {
        return [
            // Instance magic method (default)
            [false],
            // Static magic method
            [true],
        ];
    }

    /**
     * @covers ::getMagicMethods
     */
    public function testMagicMethodsReturnsEmptyCollectionWhenNoTags(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMagicMethods());

        $collection = $this->fixture->getMagicMethods();

        $this->assertEquals(0, $collection->count());
    }
}
