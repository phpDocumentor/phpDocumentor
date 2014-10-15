<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag;

class MethodAssemblerTest extends \PHPUnit_Framework_TestCase
{
    /** @var MethodAssembler */
    private $fixture;

    /** @var m\MockInterface|ProjectDescriptorBuilder */
    protected $builder;

    /**
     * Initialize fixture with its dependencies.
     */
    protected function setUp()
    {
        $this->builder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new MethodAssembler();
        $this->fixture->setBuilder($this->builder);
    }

    /**
     * @param string   $notation
     * @param string   $returnType
     * @param string   $name
     * @param string[] $arguments
     * @param string   $description
     *
     * @dataProvider provideNotations
     * @covers phpDocumentor\Descriptor\Builder\Reflector\Tags\MethodAssembler::create
     * @covers phpDocumentor\Descriptor\Builder\Reflector\Tags\MethodAssembler::createArgumentDescriptorForMagicMethod
     */
    public function testCreateMethodDescriptorFromVariousNotations(
        $notation,
        $returnType,
        $name,
        $arguments = array(),
        $description = ''
    ) {
        $this->builder->shouldReceive('buildDescriptor')
            ->with(
                m::on(
                    function (TypeCollection $value) use ($returnType) {
                        return $value[0] == $returnType;
                    }
                )
            )
            ->andReturn(new Collection(array($returnType)));

        foreach ($arguments as $argument) {
            list($argumentType, $argumentName, $argumentDefault) = $argument;
            $this->builder->shouldReceive('buildDescriptor')
                ->with(
                    m::on(
                        function (TypeCollection $value) use ($argumentType) {
                            return $value[0] == $argumentType;
                        }
                    )
                )
                ->andReturn(new Collection(array($argumentType)));
        }

        $tag = new MethodTag('method', $notation);

        $descriptor = $this->fixture->create($tag);

        $this->assertSame(1, $descriptor->getResponse()->getTypes()->count());
        $this->assertSame($returnType, $descriptor->getResponse()->getTypes()->get(0));
        $this->assertSame($name, $descriptor->getMethodName());
        $this->assertSame($description, $descriptor->getDescription());
        $this->assertSame(count($arguments), $descriptor->getArguments()->count());
        foreach ($arguments as $argument) {
            list($argumentType, $argumentName, $argumentDefault) = $argument;

            $this->assertSame($argumentType, $descriptor->getArguments()->get($argumentName)->getTypes()->get(0));
            $this->assertSame($argumentName, $descriptor->getArguments()->get($argumentName)->getName());
            $this->assertSame($argumentDefault, $descriptor->getArguments()->get($argumentName)->getDefault());
        }
    }

    /**
     * Test several different notations for the magic method.
     *
     * @return string[][]
     */
    public function provideNotations()
    {
        return array(
            // just a method without a return type
            array('myMethod()', 'void', 'myMethod'),

            // a method with two arguments
            array(
                'myMethod($argument1, $argument2)',
                'void',
                'myMethod',
                array(
                    array('mixed', '$argument1', null),
                    array('mixed', '$argument2', null),
                )
            ),

            // a method with two arguments without dollar sign
            array(
                'myMethod(argument1, argument2)',
                'void',
                'myMethod',
                array(
                    array('mixed', '$argument1', null),
                    array('mixed', '$argument2', null),
                )
            ),

            // a method without return type, but with 2 arguments and a description
            array(
                'myMethod($argument1, $argument2) This is a description.',
                'void',
                'myMethod',
                array(
                    array('mixed', '$argument1', null),
                    array('mixed', '$argument2', null),
                ),
                'This is a description.'
            ),

            // a method without return type, but with 2 arguments (with types) and a description
            array(
                'myMethod(boolean $argument1, string $argument2) This is a description.',
                'void',
                'myMethod',
                array(
                    array('boolean', '$argument1', null),
                    array('string', '$argument2', null),
                ),
                'This is a description.'
            ),

            // a method with return type, 2 arguments (with types) and a description
            array(
                'integer myMethod(boolean $argument1, string $argument2) This is a description.',
                'integer',
                'myMethod',
                array(
                    array('boolean', '$argument1', null),
                    array('string', '$argument2', null),
                ),
                'This is a description.'
            ),

            // a method with return type, 2 arguments (with types and a default value) and a description
            array(
                'integer myMethod(boolean $argument1, string $argument2 = \'test\') This is a description.',
                'integer',
                'myMethod',
                array(
                    array('boolean', '$argument1', null),
                    array('string', '$argument2', '\'test\''),
                ),
                'This is a description.'
            ),

            // a method with return type, 2 arguments (with types and a boolean default value) and a description
            array(
                'integer myMethod(boolean $argument1, string $argument2 = false) This is a description.',
                'integer',
                'myMethod',
                array(
                    array('boolean', '$argument1', null),
                    array('string', '$argument2', 'false'),
                ),
                'This is a description.'
            ),
        );
    }
}
