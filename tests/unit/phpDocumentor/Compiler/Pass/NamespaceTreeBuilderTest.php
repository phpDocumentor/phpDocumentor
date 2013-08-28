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
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;

/**
 * Tests the functionality for the ElementsIndexBuilder
 *
 * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder
 */
class NamespaceTreeBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var NamespaceTreeBuilder $fixture */
    protected $fixture;

    /** @var ProjectDescriptor */
    protected $project;

    protected function setUp()
    {
        $this->fixture = new NamespaceTreeBuilder();

        $this->project = new ProjectDescriptor('title');
        $this->project->getFiles()->add(new FileDescriptor('hash'));
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::getDescription
     */
    public function testGetDescription()
    {
        $this->assertSame(
            'Build "namespaces" index and add namespaces to "elements"',
            $this->fixture->getDescription()
        );
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::createNamespaceDescriptorTree
     */
    public function testNamespaceStringIsConvertedToTreeAndAddedToElements()
    {
        $class = new ClassDescriptor();
        $class->setNamespace('My\Space\Deeper');
        $class->setFullyQualifiedStructuralElementName('My\Space\Deeper\Class1');
        $this->project->getFiles()->get(0)->getClasses()->add($class);

        // assert that namespaces are not created in duplicate by processing two classes
        $class2 = new ClassDescriptor();
        $class2->setNamespace('My\Space\Deeper2');
        $class2->setFullyQualifiedStructuralElementName('My\Space\Deeper2\Class2');
        $this->project->getFiles()->get(0)->getClasses()->add($class2);

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $this->assertSame(
            array('~\\', '~\\My', '~\\My\\Space', '~\\My\\Space\\Deeper', '~\\My\\Space\\Deeper2'),
            array_keys($elements)
        );
        $this->assertInstanceOf(
            'phpDocumentor\Descriptor\NamespaceDescriptor',
            $this->project->getNamespace()->getChildren()->get('My')
        );
        $this->assertInstanceOf(
            'phpDocumentor\Descriptor\NamespaceDescriptor',
            $this->project->getNamespace()->getChildren()->get('My')->getChildren()->get('Space')
        );
        $this->assertSame($elements['~\\My'], $this->project->getNamespace()->getChildren()->get('My'));
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testAddClassToNamespace()
    {
        $class = new ClassDescriptor();
        $class->setNamespace('My\Space');
        $class->setFullyQualifiedStructuralElementName('My\Space\Class1');
        $this->project->getFiles()->get(0)->getClasses()->add($class);

        // double check if a second class in the same deep namespace ends up at the right location
        $class2 = new ClassDescriptor();
        $class2->setNamespace('My\Space');
        $class2->setFullyQualifiedStructuralElementName('My\Space\Class2');
        $this->project->getFiles()->get(0)->getClasses()->add($class2);

        $this->fixture->execute($this->project);

        $this->assertSame(
            array($class, $class2),
            $this->project
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getClasses()->getAll()
        );
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testAddInterfaceToNamespace()
    {
        $interface = new InterfaceDescriptor();
        $interface->setNamespace('My\Space');
        $interface->setFullyQualifiedStructuralElementName('My\Space\Interface1');
        $this->project->getFiles()->get(0)->getInterfaces()->add($interface);

        // double check if a second interface in the same deep namespace ends up at the right location
        $interface2 = new InterfaceDescriptor();
        $interface2->setNamespace('My\Space');
        $interface2->setFullyQualifiedStructuralElementName('My\Space\Interface2');
        $this->project->getFiles()->get(0)->getInterfaces()->add($interface2);

        $this->fixture->execute($this->project);

        $this->assertSame(
            array($interface, $interface2),
            $this->project
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getInterfaces()->getAll()
        );
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testAddTraitToNamespace()
    {
        $trait = new TraitDescriptor();
        $trait->setNamespace('My\Space');
        $trait->setFullyQualifiedStructuralElementName('My\Space\Trait1');
        $this->project->getFiles()->get(0)->getTraits()->add($trait);

        // double check if a second trait in the same deep namespace ends up at the right location
        $trait2 = new TraitDescriptor();
        $trait2->setNamespace('My\Space');
        $trait2->setFullyQualifiedStructuralElementName('My\Space\Trait2');
        $this->project->getFiles()->get(0)->getTraits()->add($trait2);

        $this->fixture->execute($this->project);

        $this->assertSame(
            array($trait, $trait2),
            $this->project
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getTraits()->getAll()
        );
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testAddConstantToNamespace()
    {
        $constant = new ConstantDescriptor();
        $constant->setNamespace('My\Space');
        $constant->setFullyQualifiedStructuralElementName('My\Space\Constant1');
        $this->project->getFiles()->get(0)->getConstants()->add($constant);

        // double check if a second constant in the same deep namespace ends up at the right location
        $constant2 = new ConstantDescriptor();
        $constant2->setNamespace('My\Space');
        $constant2->setFullyQualifiedStructuralElementName('My\Space\Constant2');
        $this->project->getFiles()->get(0)->getConstants()->add($constant2);

        $this->fixture->execute($this->project);

        $this->assertSame(
            array($constant, $constant2),
            $this->project
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getConstants()->getAll()
        );
    }

    /**
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testAddFunctionToNamespace()
    {
        $function = new FunctionDescriptor();
        $function->setNamespace('My\Space');
        $function->setFullyQualifiedStructuralElementName('My\Space\Function1');
        $this->project->getFiles()->get(0)->getFunctions()->add($function);

        // double check if a second function in the same deep namespace ends up at the right location
        $function2 = new FunctionDescriptor();
        $function2->setNamespace('My\Space');
        $function2->setFullyQualifiedStructuralElementName('My\Space\Function2');
        $this->project->getFiles()->get(0)->getFunctions()->add($function2);

        $this->fixture->execute($this->project);

        $this->assertSame(
            array($function, $function2),
            $this->project
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getFunctions()->getAll()
        );
    }
}
