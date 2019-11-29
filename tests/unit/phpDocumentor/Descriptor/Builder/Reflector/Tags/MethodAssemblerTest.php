<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */
namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\Tags\Method;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Reflection\Types\Void_;
use function count;

class MethodAssemblerTest extends MockeryTestCase
{
    /** @var MethodAssembler */
    private $fixture;

    /** @var m\MockInterface|ProjectDescriptorBuilder */
    protected $builder;

    /**
     * Initialize fixture with its dependencies.
     */
    protected function setUp() : void
    {
        $this->builder = m::mock(ProjectDescriptorBuilder::class);
        $this->fixture = new MethodAssembler();
        $this->fixture->setBuilder($this->builder);
    }

    /**
     * @param string[] $arguments
     * @param string   $description
     *
     * @dataProvider provideNotations
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\MethodAssembler::create
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\Tags\MethodAssembler::createArgumentDescriptorForMagicMethod
     */
    public function testCreateMethodDescriptorFromVariousNotations(
        Type $returnType,
        string $name,
        array $arguments = [],
        ?Description $description = null
    ) : void {
        $tag = new Method($name, $arguments, $returnType, false, $description);

        $descriptor = $this->fixture->create($tag);

        $this->assertEquals($returnType, $descriptor->getResponse()->getType());
        $this->assertSame($name, $descriptor->getMethodName());
        $this->assertSame((string) $description, $descriptor->getDescription());
        $this->assertSame(count($arguments), $descriptor->getArguments()->count());
        foreach ($arguments as $argument) {
            $this->assertSame($argument['type'], $descriptor->getArguments()->get($argument['name'])->getType());
            $this->assertSame($argument['name'], $descriptor->getArguments()->get($argument['name'])->getName());
        }
    }

    /**
     * Test several different notations for the magic method.
     *
     * @return string[][]
     */
    public function provideNotations() : array
    {
        return [
            // just a method without a return type
            [new Void_(), 'myMethod'],

            // a method with two arguments
            [
                new Void_(),
                'myMethod',
                [
                    ['name' => '$argument1', 'type' => new Mixed_()],
                    ['name' => '$argument2', 'type' => new Mixed_()],
                ],
            ],

            // a method with two arguments without dollar sign
            [
                new Void_(),
                'myMethod',
                [
                    ['name' => '$argument1', 'type' => new Mixed_()],
                    ['name' => '$argument2', 'type' => new Mixed_()],
                ],
            ],

            // a method without return type, but with 2 arguments and a description
            [
                new Void_(),
                'myMethod',
                [
                    ['name' => '$argument1', 'type' => new Mixed_()],
                    ['name' => '$argument2', 'type' => new Mixed_()],
                ],
                new Description('This is a description.'),
            ],

            // a method without return type, but with 2 arguments (with types) and a description
            [
                new Void_(),
                'myMethod',
                [
                    ['name' => '$argument1', 'type' => new Boolean()],
                    ['name' => '$argument2', 'type' => new String_()],
                ],
                new Description('This is a description.'),
            ],

            // a method with return type, 2 arguments (with types) and a description
            [
                new Integer(),
                'myMethod',
                [
                    ['name' => '$argument1', 'type' => new Boolean()],
                    ['name' => '$argument2', 'type' => new String_()],
                ],
                new Description('This is a description.'),
            ],
        ];
    }
}
