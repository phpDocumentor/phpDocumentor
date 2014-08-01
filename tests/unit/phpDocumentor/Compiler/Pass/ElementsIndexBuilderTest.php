<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use Mockery as m;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;

/**
 * Tests the functionality for the ElementsIndexBuilder
 *
 * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder
 */
class ElementsIndexBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ElementsIndexBuilder $fixture */
    protected $fixture;

    /** @var ProjectDescriptor */
    protected $project;

    protected function setUp()
    {
        $this->fixture = new ElementsIndexBuilder();

        $this->project = new ProjectDescriptor('title');
        $file1 = new FileDescriptor('hash');
        $file2 = new FileDescriptor('hash2');
        $this->project->getFiles()->add($file1);
        $this->project->getFiles()->add($file2);
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getDescription
     */
    public function testGetDescription()
    {
        $this->assertSame('Build "elements" index', $this->fixture->getDescription());
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddClassesToIndex()
    {
        $file1 = $this->project->getFiles()->get(0);
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setFullyQualifiedStructuralElementName('My\Space\Class1');
        $file1->getClasses()->add($classDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $classDescriptor2 = new ClassDescriptor();
        $classDescriptor2->setFullyQualifiedStructuralElementName('My\Space\Class2');
        $file2->getClasses()->add($classDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(array('My\Space\Class1', 'My\Space\Class2'), array_keys($elements));
        $this->assertSame(array($classDescriptor1, $classDescriptor2), array_values($elements));

        $elements = $this->project->getIndexes()->get('classes')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(array('My\Space\Class1', 'My\Space\Class2'), array_keys($elements));
        $this->assertSame(array($classDescriptor1, $classDescriptor2), array_values($elements));
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddInterfacesToIndex()
    {
        $file1 = $this->project->getFiles()->get(0);
        $interfaceDescriptor1 = new InterfaceDescriptor();
        $interfaceDescriptor1->setFullyQualifiedStructuralElementName('My\Space\Interface1');
        $file1->getInterfaces()->add($interfaceDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $interfaceDescriptor2 = new InterfaceDescriptor();
        $interfaceDescriptor2->setFullyQualifiedStructuralElementName('My\Space\Interface2');
        $file2->getInterfaces()->add($interfaceDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(array('My\Space\Interface1', 'My\Space\Interface2'), array_keys($elements));
        $this->assertSame(array($interfaceDescriptor1, $interfaceDescriptor2), array_values($elements));

        $elements = $this->project->getIndexes()->get('interfaces')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(array('My\Space\Interface1', 'My\Space\Interface2'), array_keys($elements));
        $this->assertSame(array($interfaceDescriptor1, $interfaceDescriptor2), array_values($elements));
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddTraitsToIndex()
    {
        $file1 = $this->project->getFiles()->get(0);
        $traitDescriptor1 = new TraitDescriptor();
        $traitDescriptor1->setFullyQualifiedStructuralElementName('My\Space\Trait1');
        $file1->getTraits()->add($traitDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $traitDescriptor2 = new TraitDescriptor();
        $traitDescriptor2->setFullyQualifiedStructuralElementName('My\Space\Trait2');
        $file2->getTraits()->add($traitDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(array('My\Space\Trait1', 'My\Space\Trait2'), array_keys($elements));
        $this->assertSame(array($traitDescriptor1, $traitDescriptor2), array_values($elements));

        $elements = $this->project->getIndexes()->get('traits')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(array('My\Space\Trait1', 'My\Space\Trait2'), array_keys($elements));
        $this->assertSame(array($traitDescriptor1, $traitDescriptor2), array_values($elements));
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddFunctionsToIndex()
    {
        $file1 = $this->project->getFiles()->get(0);
        $functionDescriptor1 = new FunctionDescriptor();
        $functionDescriptor1->setFullyQualifiedStructuralElementName('function1');
        $file1->getFunctions()->add($functionDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $functionDescriptor2 = new FunctionDescriptor();
        $functionDescriptor2->setFullyQualifiedStructuralElementName('function2');
        $file2->getFunctions()->add($functionDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(array('function1', 'function2'), array_keys($elements));
        $this->assertSame(array($functionDescriptor1, $functionDescriptor2), array_values($elements));

        $elements = $this->project->getIndexes()->get('functions')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(array('function1', 'function2'), array_keys($elements));
        $this->assertSame(array($functionDescriptor1, $functionDescriptor2), array_values($elements));
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddConstantsToIndex()
    {
        $file1 = $this->project->getFiles()->get(0);
        $constantDescriptor1 = new ConstantDescriptor();
        $constantDescriptor1->setFullyQualifiedStructuralElementName('CONSTANT1');
        $file1->getConstants()->add($constantDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $constantDescriptor2 = new ConstantDescriptor();
        $constantDescriptor2->setFullyQualifiedStructuralElementName('CONSTANT2');
        $file2->getConstants()->add($constantDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(array('CONSTANT1', 'CONSTANT2'), array_keys($elements));
        $this->assertSame(array($constantDescriptor1, $constantDescriptor2), array_values($elements));

        $elements = $this->project->getIndexes()->get('constants')->getAll();
        $this->assertCount(2, $elements);
        $this->assertSame(array('CONSTANT1', 'CONSTANT2'), array_keys($elements));
        $this->assertSame(array($constantDescriptor1, $constantDescriptor2), array_values($elements));
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getSubElements
     */
    public function testAddClassConstantsToIndex()
    {
        $file1 = $this->project->getFiles()->get(0);
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setFullyQualifiedStructuralElementName('My\Space\Class1');
        $file1->getClasses()->add($classDescriptor1);

        $classConstantDescriptor1 = new ConstantDescriptor();
        $classConstantDescriptor1->setFullyQualifiedStructuralElementName('My\Space\Class1::CONSTANT');
        $classDescriptor1->getConstants()->add($classConstantDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $classDescriptor2 = new ClassDescriptor();
        $classDescriptor2->setFullyQualifiedStructuralElementName('My\Space\Class2');
        $file2->getClasses()->add($classDescriptor2);

        $classConstantDescriptor2 = new ConstantDescriptor();
        $classConstantDescriptor2->setFullyQualifiedStructuralElementName('My\Space\Class2::CONSTANT');
        $classDescriptor2->getConstants()->add($classConstantDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(4, $elements);
        $this->assertSame(
            array('My\Space\Class1', 'My\Space\Class1::CONSTANT', 'My\Space\Class2', 'My\Space\Class2::CONSTANT'),
            array_keys($elements)
        );
        $this->assertSame(
            array($classDescriptor1, $classConstantDescriptor1, $classDescriptor2, $classConstantDescriptor2),
            array_values($elements)
        );

        // class constants are not indexed separately
        $elements = $this->project->getIndexes()->get('constants')->getAll();
        $this->assertCount(0, $elements);
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getSubElements
     */
    public function testAddPropertiesToIndex()
    {
        $file1 = $this->project->getFiles()->get(0);
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setFullyQualifiedStructuralElementName('My\Space\Class1');
        $file1->getClasses()->add($classDescriptor1);

        $classPropertyDescriptor1 = new PropertyDescriptor();
        $classPropertyDescriptor1->setFullyQualifiedStructuralElementName('My\Space\Class1::PROPERTY');
        $classDescriptor1->getProperties()->add($classPropertyDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $classDescriptor2 = new ClassDescriptor();
        $classDescriptor2->setFullyQualifiedStructuralElementName('My\Space\Class2');
        $file2->getClasses()->add($classDescriptor2);

        $classPropertyDescriptor2 = new PropertyDescriptor();
        $classPropertyDescriptor2->setFullyQualifiedStructuralElementName('My\Space\Class2::PROPERTY');
        $classDescriptor2->getProperties()->add($classPropertyDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(4, $elements);
        // note the addition of the dollar sign in front of the property name
        $this->assertSame(
            array('My\Space\Class1', 'My\Space\Class1::$PROPERTY', 'My\Space\Class2', 'My\Space\Class2::$PROPERTY'),
            array_keys($elements)
        );
        $this->assertSame(
            array($classDescriptor1, $classPropertyDescriptor1, $classDescriptor2, $classPropertyDescriptor2),
            array_values($elements)
        );

        // class properties are not indexed separately
        $this->assertNull($this->project->getIndexes()->get('properties'));
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getSubElements
     */
    public function testAddMethodsToIndex()
    {
        $file1 = $this->project->getFiles()->get(0);
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setFullyQualifiedStructuralElementName('My\Space\Class1');
        $file1->getClasses()->add($classDescriptor1);

        $classMethodDescriptor1 = new MethodDescriptor();
        $classMethodDescriptor1->setFullyQualifiedStructuralElementName('My\Space\Class1::METHOD');
        $classDescriptor1->getMethods()->add($classMethodDescriptor1);

        $file2 = $this->project->getFiles()->get(1);
        $classDescriptor2 = new ClassDescriptor();
        $classDescriptor2->setFullyQualifiedStructuralElementName('My\Space\Class2');
        $file2->getClasses()->add($classDescriptor2);

        $classMethodDescriptor2 = new MethodDescriptor();
        $classMethodDescriptor2->setFullyQualifiedStructuralElementName('My\Space\Class2::METHOD');
        $classDescriptor2->getMethods()->add($classMethodDescriptor2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertCount(4, $elements);
        $this->assertSame(
            array('My\Space\Class1', 'My\Space\Class1::METHOD', 'My\Space\Class2', 'My\Space\Class2::METHOD'),
            array_keys($elements)
        );
        $this->assertSame(
            array($classDescriptor1, $classMethodDescriptor1, $classDescriptor2, $classMethodDescriptor2),
            array_values($elements)
        );

        // class methods are not indexed separately
        $this->assertNull($this->project->getIndexes()->get('methods'));
    }
}
