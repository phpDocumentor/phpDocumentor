<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Tag\MethodDescriptor as TagMethodDescriptor;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\Attributes\DataProvider;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

trait MagicMethodContainerTests
{
    use ProphecyTrait;

    #[DataProvider('provideMagicMethodProperties')]
    public function testGetMagicMethods(bool $isStatic): void
    {
        $methodName = 'methodName';
        $description = new DescriptionDescriptor(new Description('description'), []);
        $response = new ReturnDescriptor('return');
        $response->setType(new String_());
        $argument = $this->prophesize(ArgumentDescriptor::class);
        $argument->setMethod(Argument::any())->shouldBeCalled();

        $methodMock = $this->prophesize(TagMethodDescriptor::class);
        $methodMock->getMethodName()->willReturn($methodName);
        $methodMock->getDescription()->willReturn($description);
        $methodMock->getResponse()->willReturn($response);
        $methodMock->getArguments()->willReturn(new Collection(['argument1' => $argument->reveal()]));
        $methodMock->isStatic()->willReturn($isStatic);
        $methodMock->getHasReturnByReference()->willReturn(false);

        $this->fixture->getTags()->fetch('method', new Collection())->add($methodMock->reveal());

        $magicMethods = $this->fixture->getMagicMethods();

        $this->assertCount(1, $magicMethods);
    }

    /**
     * Provider to test different properties for a class magic method
     * (provides isStatic)
     *
     * @return bool[][]
     */
    public static function provideMagicMethodProperties(): array
    {
        return [
            // Instance magic method (default)
            [false],
            // Static magic method
            [true],
        ];
    }

    public function testMagicMethodsReturnsEmptyCollectionWhenNoTags(): void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getMagicMethods());

        $collection = $this->fixture->getMagicMethods();

        $this->assertEquals(0, $collection->count());
    }
}
