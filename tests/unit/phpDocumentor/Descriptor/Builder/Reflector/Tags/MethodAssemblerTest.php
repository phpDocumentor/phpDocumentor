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

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use phpDocumentor\Reflection\DocBlock\Tags\MethodParameter;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Void_;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

use function count;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\Tags\MethodAssembler

 */
final class MethodAssemblerTest extends TestCase
{
    use ProphecyTrait;

    private MethodAssembler $fixture;

    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    protected $builder;

    /**
     * Initialize fixture with its dependencies.
     */
    protected function setUp(): void
    {
        $this->builder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->fixture = new MethodAssembler();
        $this->fixture->setBuilder($this->builder->reveal());
    }

    /**
     * @param MethodParameter[] $arguments
     * @param string $description
     *
     * @dataProvider provideNotations
     * @covers       ::create
     * @covers       ::buildDescriptor
     */
    public function testCreateMethodDescriptorFromVariousNotations(
        Type $returnType,
        string $name,
        array $arguments = [],
        Description|null $description = null,
    ): void {
        $tag = new Method($name, [], $returnType, false, $description, false, $arguments);

        $descriptor = $this->fixture->create($tag);

        $this->assertEquals($returnType, $descriptor->getResponse()->getType());
        $this->assertSame($name, $descriptor->getMethodName());
        $this->assertEquals($description ?? '', (string) $descriptor->getDescription());
        $this->assertSame(count($arguments), $descriptor->getArguments()->count());
        foreach ($arguments as $argument) {
            $this->assertSame($argument->getType(), $descriptor->getArguments()->get($argument->getName())->getType());
            $this->assertSame($argument->getName(), $descriptor->getArguments()->get($argument->getName())->getName());
        }
    }

    /**
     * Test several different notations for the magic method.
     *
     * @return string[][]
     */
    public static function provideNotations(): array
    {
        return [
            // just a method without a return type
            [new Void_(), 'myMethod'],

            // a method with two arguments
            [
                new Void_(),
                'myMethod',
                [
                    new MethodParameter('$argument1', new Mixed_()),
                    new MethodParameter('$argument2', new Mixed_()),
                ],
            ],

            // a method with two arguments without dollar sign
            [
                new Void_(),
                'myMethod',
                [
                    new MethodParameter('$argument1', new Mixed_()),
                    new MethodParameter('$argument2', new Mixed_()),
                ],
            ],

            // a method without return type, but with 2 arguments and a description
            [
                new Void_(),
                'myMethod',
                [
                    new MethodParameter('$argument1', new Mixed_()),
                    new MethodParameter('$argument2', new Mixed_()),
                ],
                new Description('This is a description.'),
            ],

            // a method without return type, but with 2 arguments (with types) and a description
            [
                new Void_(),
                'myMethod',
                [
                    new MethodParameter('$argument1', new Boolean()),
                    new MethodParameter('$argument2', new String_()),
                ],
                new Description('This is a description.'),
            ],

            // a method with return type, 2 arguments (with types) and a description
            [
                new Integer(),
                'myMethod',
                [
                    new MethodParameter('$argument1', new Boolean()),
                    new MethodParameter('$argument2', new String_()),
                ],
                new Description('This is a description.'),
            ],
        ];
    }
}
