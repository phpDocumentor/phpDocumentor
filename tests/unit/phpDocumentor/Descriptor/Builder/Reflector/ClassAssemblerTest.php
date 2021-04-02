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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ApiSetDescriptorBuilder;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Constant;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Property;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Test class for \phpDocumentor\Descriptor\Builder
 *
 * @covers \phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler
 */
class ClassAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ClassAssembler $fixture */
    protected $fixture;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp(): void
    {
        $this->fixture = new ClassAssembler();
        $this->fixture->setBuilder($this->getApiSetDescriptorBuilderMock()->reveal());
    }

    /**
     * Creates a Descriptor from a provided class.
     *
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler::create
     */
    public function testCreateClassDescriptorFromReflector(): void
    {
        $name = 'ClassName';
        $namespace = 'Namespace';
        $docBlockDescriptionContent = <<<DOCBLOCK
/**
 * This is a example description
 */
DOCBLOCK;

        $classReflectorMock = $this->getClassReflectorDescriptor();

        $descriptor = $this->fixture->create($classReflectorMock);

        $this->assertSame(
            '\\' . $namespace . '\\' . $name,
            (string) $descriptor->getFullyQualifiedStructuralElementName()
        );
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($docBlockDescriptionContent, (string) $descriptor->getDescription());
    }

    /**
     * Creates a Descriptor from a provided class.
     *
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler::create
     */
    public function testParentIsOmittedWhenEqualToItself(): void
    {
        $name = 'ClassName';
        $namespace = 'Namespace';
        $classFqsen = new Fqsen('\\' . $namespace . '\\' . $name);
        $docBlockDescriptionContent = <<<DOCBLOCK
/**
 * This is a example description
 */
DOCBLOCK;

        $classReflectorMock = $this->getClassReflectorDescriptor($classFqsen, $classFqsen);

        $descriptor = $this->fixture->create($classReflectorMock);

        $this->assertSame(
            '\\' . $namespace . '\\' . $name,
            (string) $descriptor->getFullyQualifiedStructuralElementName()
        );
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($docBlockDescriptionContent, (string) $descriptor->getDescription());
        $this->assertSame(null, $descriptor->getParent());
    }

    /**
     * Create a ClassReflector mock
     */
    protected function getClassReflectorDescriptor(?Fqsen $classFqsen = null, ?Fqsen $parent = null): Class_
    {
        $docBlockDescriptionContent = <<<DOCBLOCK
/**
 * This is a example description
 */
DOCBLOCK;
        $docBlockMock = new DocBlock(
            'This is a example description',
            new DocBlock\Description($docBlockDescriptionContent),
            []
        );

        if ($classFqsen === null) {
            $name = 'ClassName';
            $namespace = 'Namespace';
            $classFqsen = new Fqsen('\\' . $namespace . '\\' . $name);
        }

        $classReflectorMock = new Class_(
            $classFqsen,
            $docBlockMock,
            $parent
        );

        $classReflectorMock->addConstant(new Constant(new Fqsen($classFqsen . '::Constant')));
        $classReflectorMock->addInterface(new Fqsen('\\TestInterface'));
        $classReflectorMock->addProperty(new Property(new Fqsen($classFqsen . '::$property')));
        $classReflectorMock->addMethod(new Method(new Fqsen($classFqsen . '::method()')));

        return $classReflectorMock;
    }

    /**
     * Create a descriptor builder mock
     */
    protected function getApiSetDescriptorBuilderMock(): ObjectProphecy
    {
        $ApiSetDescriptorBuilderMock = $this->prophesize(ApiSetDescriptorBuilder::class);
        $ApiSetDescriptorBuilderMock->getDefaultPackage()->shouldBeCalled()->willReturn('\\');
        $ApiSetDescriptorBuilderMock->buildDescriptor(Argument::any(), Argument::any())->will(function ($param) {
            switch ($param) {
                case 'Method':
                    $mock = $this->prophesize(MethodDescriptor::class);
                    $mock->getName()->shouldBeCalledOnce()->willReturn('Mock');
                    $mock->setParent()->shouldBeCalledOnce()->willReturn();
                    break;

                case 'Constant':
                    $mock = $this->prophesize(ConstantDescriptor::class);
                    $mock->getName()->shouldBeCalledOnce()->willReturn('Mock');
                    $mock->setParent()->shouldBeCalledOnce()->willReturn();
                    break;
                default:
                    return null;
            }

            return $mock->reveal();
        });

        return $ApiSetDescriptorBuilderMock;
    }
}
