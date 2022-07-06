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

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function current;
use function next;
use function sort;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder
 * @covers ::<private>
 * @covers ::<protected>
 */
class NamespaceTreeBuilderTest extends TestCase
{
    use Faker;

    /** @var NamespaceTreeBuilder $fixture */
    protected $fixture;

    /** @var ApiSetDescriptor */
    protected $project;

    protected function setUp(): void
    {
        $this->fixture = new NamespaceTreeBuilder();

        $this->project = $this->faker()->apiSetDescriptorWithFiles();
    }

    /**
     * @covers ::getDescription
     */
    public function testGetDescription(): void
    {
        self::assertSame(
            'Build "namespaces" index and add namespaces to "elements"',
            $this->fixture->getDescription()
        );
    }

    /**
     * @covers ::execute
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testNamespaceStringIsConvertedToTreeAndAddedToElements(): void
    {
        $classes[] = $this->faker()->classDescriptor('\My\Space\Deeper\Class1', '\My\Space\Deeper');
        $classes[] = $this->faker()->classDescriptor('\My\Space\Deeper2\Class2', '\My\Space\Deeper2');

        foreach ($this->project->getFiles() as $file) {
            $file->getClasses()->add(current($classes));
            next($classes);
        }

        $this->fixture->execute($this->project);

        $elements = $this->project->getIndexes()->get('elements')->getAll();
        $elementNames = array_keys($elements);
        sort($elementNames);
        self::assertSame(
            ['~\\', '~\\My', '~\\My\\Space', '~\\My\\Space\\Deeper', '~\\My\\Space\\Deeper2'],
            $elementNames
        );
        self::assertInstanceOf(
            NamespaceDescriptor::class,
            $this->project->getNamespace()->getChildren()->get('My')
        );
        self::assertInstanceOf(
            NamespaceDescriptor::class,
            $this->project->getNamespace()->getChildren()->get('My')->getChildren()->get('Space')
        );
        self::assertSame($elements['~\\My'], $this->project->getNamespace()->getChildren()->get('My'));
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testAddClassToNamespace(): void
    {
        $classes[] = $this->faker()->classDescriptor('\My\Space\Class1', '\My\Space');
        $classes[] = $this->faker()->classDescriptor('\My\Space\Class2', '\My\Space');

        foreach ($this->project->getFiles() as $file) {
            $file->getClasses()->add(current($classes));
            next($classes);
        }

        $this->fixture->execute($this->project);

        self::assertSame(
            $classes,
            $this->project
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getClasses()->getAll()
        );
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testAddInterfaceToNamespace(): void
    {
        $classes[] = $this->faker()->interfaceDescriptor('\My\Space\Class1', '\My\Space');
        $classes[] = $this->faker()->interfaceDescriptor('\My\Space\Class2', '\My\Space');

        foreach ($this->project->getFiles() as $file) {
            $file->getInterfaces()->add(current($classes));
            next($classes);
        }

        $this->fixture->execute($this->project);

        self::assertSame(
            $classes,
            $this->project
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getInterfaces()->getAll()
        );
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testAddTraitToNamespace(): void
    {
        $classes[] = $this->faker()->traitDescriptor('\My\Space\Class1', '\My\Space');
        $classes[] = $this->faker()->traitDescriptor('\My\Space\Class2', '\My\Space');

        foreach ($this->project->getFiles() as $file) {
            $file->getTraits()->add(current($classes));
            next($classes);
        }

        $this->fixture->execute($this->project);

        self::assertSame(
            $classes,
            $this->project
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getTraits()->getAll()
        );
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testAddConstantToNamespace(): void
    {
        $classes[] = $this->faker()->constantDescriptor('\My\Space\Class1', '\My\Space');
        $classes[] = $this->faker()->constantDescriptor('\My\Space\Class2', '\My\Space');

        foreach ($this->project->getFiles() as $file) {
            $file->getConstants()->add(current($classes));
            next($classes);
        }

        $this->fixture->execute($this->project);

        self::assertSame(
            $classes,
            $this->project
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getConstants()->getAll()
        );
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\NamespaceTreeBuilder::addElementsOfTypeToNamespace
     */
    public function testAddFunctionToNamespace(): void
    {
        $classes[] = $this->faker()->functionDescriptor('\My\Space\Class1', '\My\Space');
        $classes[] = $this->faker()->functionDescriptor('\My\Space\Class2', '\My\Space');

        foreach ($this->project->getFiles() as $file) {
            $file->getFunctions()->add(current($classes));
            next($classes);
        }

        $this->fixture->execute($this->project);

        self::assertSame(
            $classes,
            $this->project
                ->getNamespace()->getChildren()
                ->get('My')->getChildren()
                ->get('Space')->getFunctions()->getAll()
        );
    }
}
