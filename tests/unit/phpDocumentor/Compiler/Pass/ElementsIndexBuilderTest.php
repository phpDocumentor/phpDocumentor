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

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;
use function array_keys;
use function array_values;

/**
 * Tests the functionality for the ElementsIndexBuilder
 *
 * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder
 */
class ElementsIndexBuilderTest extends TestCase
{
    /** @var ElementsIndexBuilder $fixture */
    protected $fixture;

    /** @var ProjectDescriptor */
    protected $project;

    protected function setUp(): void
    {
        $this->fixture = new ElementsIndexBuilder();

        $this->project = new ProjectDescriptor('title');
        $file1 = new FileDescriptor('hash');
        $file2 = new FileDescriptor('hash2');
        $this->project->getFiles()->add($file1);
        $this->project->getFiles()->add($file2);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getDescription
     */
    public function testGetDescription(): void
    {
        $this->assertSame('Build "elements" index', $this->fixture->getDescription());
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddClassesToIndex(): void
    {
        $file1 = $this->project->getFiles()->get(0);
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class1'));
        $file1->getClasses()->add($classDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $classDescriptor2 = new ClassDescriptor();
        $classDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class2'));
        $file2->getClasses()->add($classDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(['\My\Space\Class1', '\My\Space\Class2'], array_keys($elements));
        $this->assertSame([$classDescriptor1, $classDescriptor2], array_values($elements));

        $elements = $this->project->getIndexes()->get('classes')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(['\My\Space\Class1', '\My\Space\Class2'], array_keys($elements));
        $this->assertSame([$classDescriptor1, $classDescriptor2], array_values($elements));
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddInterfacesToIndex(): void
    {
        $file1 = $this->project->getFiles()->get(0);
        $interfaceDescriptor1 = new InterfaceDescriptor();
        $interfaceDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Interface1'));
        $file1->getInterfaces()->add($interfaceDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $interfaceDescriptor2 = new InterfaceDescriptor();
        $interfaceDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Interface2'));
        $file2->getInterfaces()->add($interfaceDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(['\My\Space\Interface1', '\My\Space\Interface2'], array_keys($elements));
        $this->assertSame([$interfaceDescriptor1, $interfaceDescriptor2], array_values($elements));

        $elements = $this->project->getIndexes()->get('interfaces')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(['\My\Space\Interface1', '\My\Space\Interface2'], array_keys($elements));
        $this->assertSame([$interfaceDescriptor1, $interfaceDescriptor2], array_values($elements));
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddTraitsToIndex(): void
    {
        $file1 = $this->project->getFiles()->get(0);
        $traitDescriptor1 = new TraitDescriptor();
        $traitDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Trait1'));
        $file1->getTraits()->add($traitDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $traitDescriptor2 = new TraitDescriptor();
        $traitDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Trait2'));
        $file2->getTraits()->add($traitDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(['\My\Space\Trait1', '\My\Space\Trait2'], array_keys($elements));
        $this->assertSame([$traitDescriptor1, $traitDescriptor2], array_values($elements));

        $elements = $this->project->getIndexes()->get('traits')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(['\My\Space\Trait1', '\My\Space\Trait2'], array_keys($elements));
        $this->assertSame([$traitDescriptor1, $traitDescriptor2], array_values($elements));
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddFunctionsToIndex(): void
    {
        $file1 = $this->project->getFiles()->get(0);
        $functionDescriptor1 = new FunctionDescriptor();
        $functionDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\function1'));
        $file1->getFunctions()->add($functionDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $functionDescriptor2 = new FunctionDescriptor();
        $functionDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\function2'));
        $file2->getFunctions()->add($functionDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(['\function1', '\function2'], array_keys($elements));
        $this->assertSame([$functionDescriptor1, $functionDescriptor2], array_values($elements));

        $elements = $this->project->getIndexes()->get('functions')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(['\function1', '\function2'], array_keys($elements));
        $this->assertSame([$functionDescriptor1, $functionDescriptor2], array_values($elements));
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddConstantsToIndex(): void
    {
        $file1 = $this->project->getFiles()->get(0);
        $constantDescriptor1 = new ConstantDescriptor();
        $constantDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\CONSTANT1'));
        $file1->getConstants()->add($constantDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $constantDescriptor2 = new ConstantDescriptor();
        $constantDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\CONSTANT2'));
        $file2->getConstants()->add($constantDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(['\CONSTANT1', '\CONSTANT2'], array_keys($elements));
        $this->assertSame([$constantDescriptor1, $constantDescriptor2], array_values($elements));

        $elements = $this->project->getIndexes()->get('constants')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(['\CONSTANT1', '\CONSTANT2'], array_keys($elements));
        $this->assertSame([$constantDescriptor1, $constantDescriptor2], array_values($elements));
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getSubElements
     */
    public function testAddClassConstantsToIndex(): void
    {
        $file1 = $this->project->getFiles()->get(0);
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class1'));
        $file1->getClasses()->add($classDescriptor1);

        $classConstantDescriptor1 = new ConstantDescriptor();
        $classConstantDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class1::CONSTANT'));
        $classDescriptor1->getConstants()->add($classConstantDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $classDescriptor2 = new ClassDescriptor();
        $classDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class2'));
        $file2->getClasses()->add($classDescriptor2);

        $classConstantDescriptor2 = new ConstantDescriptor();
        $classConstantDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class2::CONSTANT'));
        $classDescriptor2->getConstants()->add($classConstantDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(4, $elements);
        $this->assertSame(
            ['\My\Space\Class1', '\My\Space\Class1::CONSTANT', '\My\Space\Class2', '\My\Space\Class2::CONSTANT'],
            array_keys($elements)
        );
        $this->assertSame(
            [$classDescriptor1, $classConstantDescriptor1, $classDescriptor2, $classConstantDescriptor2],
            array_values($elements)
        );

        // class constants are not indexed separately
        $elements = $this->project->getIndexes()->get('constants')->getAll();
        $this->assertCount(0, $elements);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getSubElements
     */
    public function testAddPropertiesToIndex(): void
    {
        $file1 = $this->project->getFiles()->get(0);
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class1'));
        $file1->getClasses()->add($classDescriptor1);

        $classPropertyDescriptor1 = new PropertyDescriptor();
        $classPropertyDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class1::$property'));
        $classDescriptor1->getProperties()->add($classPropertyDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $classDescriptor2 = new ClassDescriptor();
        $classDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class2'));
        $file2->getClasses()->add($classDescriptor2);

        $classPropertyDescriptor2 = new PropertyDescriptor();
        $classPropertyDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class2::$property'));
        $classDescriptor2->getProperties()->add($classPropertyDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(4, $elements);
        $this->assertSame(
            ['\My\Space\Class1', '\My\Space\Class1::$property', '\My\Space\Class2', '\My\Space\Class2::$property'],
            array_keys($elements)
        );
        $this->assertSame(
            [$classDescriptor1, $classPropertyDescriptor1, $classDescriptor2, $classPropertyDescriptor2],
            array_values($elements)
        );

        // class properties are not indexed separately
        $this->assertNull($this->project->getIndexes()->fetch('properties'));
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getSubElements
     */
    public function testAddMethodsToIndex(): void
    {
        $file1 = $this->project->getFiles()->get(0);
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class1'));
        $file1->getClasses()->add($classDescriptor1);

        $classMethodDescriptor1 = new MethodDescriptor();
        $classMethodDescriptor1->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class1::METHOD'));
        $classDescriptor1->getMethods()->add($classMethodDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $classDescriptor2 = new ClassDescriptor();
        $classDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class2'));
        $file2->getClasses()->add($classDescriptor2);

        $classMethodDescriptor2 = new MethodDescriptor();
        $classMethodDescriptor2->setFullyQualifiedStructuralElementName(new Fqsen('\My\Space\Class2::METHOD'));
        $classDescriptor2->getMethods()->add($classMethodDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(4, $elements);
        $this->assertSame(
            ['\My\Space\Class1', '\My\Space\Class1::METHOD', '\My\Space\Class2', '\My\Space\Class2::METHOD'],
            array_keys($elements)
        );
        $this->assertSame(
            [$classDescriptor1, $classMethodDescriptor1, $classDescriptor2, $classMethodDescriptor2],
            array_values($elements)
        );

        // class methods are not indexed separately
        $this->assertNull($this->project->getIndexes()->fetch('methods'));
    }
}
