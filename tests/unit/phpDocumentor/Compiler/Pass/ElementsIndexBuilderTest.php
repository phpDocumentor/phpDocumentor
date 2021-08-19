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
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;

use function array_keys;
use function array_merge;
use function array_values;
use function count;
use function current;
use function next;
use function sort;

/**
 * Tests the functionality for the ElementsIndexBuilder
 *
 * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder
 */
class ElementsIndexBuilderTest extends TestCase
{
    use Faker;

    /** @var ElementsIndexBuilder $fixture */
    protected $fixture;

    /** @var ApiSetDescriptor */
    protected $project;

    /**
     * Will compare $elements with $expectedElementNames and $expectedDescriptors
     *
     * The provided values are sorted, so order is ignored.
     *
     * @param Descriptor[] $elements
     * @param string[] $expectedElementNames
     * @param Descriptor[] $expectedDescriptors
     */
    private static function assertSameElements(array $elements, array $expectedElementNames, array $expectedDescriptors): void
    {
        $actualNames = array_keys($elements);
        $actualElements = array_values($elements);

        sort($actualNames);
        sort($actualElements);
        sort($expectedDescriptors);
        sort($expectedElementNames);

        self::assertCount(count($expectedElementNames), $elements);
        self::assertSame($expectedElementNames, $actualNames);
        self::assertSame($expectedDescriptors, $actualElements);
    }

    protected function setUp(): void
    {
        $this->fixture = new ElementsIndexBuilder();
        $this->project = $this->faker()->apiSetDescriptorWithFiles();
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getDescription
     */
    public function testGetDescription(): void
    {
        self::assertSame('Build "elements" index', $this->fixture->getDescription());
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddClassesToIndex(): void
    {
        $expectedElementNames = ['\My\Space\Class1', '\My\Space\Class2'];
        $expectedDescriptors = [];
        foreach ($this->project->getFiles() as $file) {
            $descriptor = $this->faker()->classDescriptor(current($expectedElementNames));
            $expectedDescriptors[] = $descriptor;

            $file->getClasses()->add($descriptor);
            next($expectedElementNames);
        }

        $this->fixture->execute($this->project);

        self::assertSameElements(
            $this->project->getIndexes()->get('elements')->getAll(),
            $expectedElementNames,
            $expectedDescriptors
        );
        self::assertSameElements(
            $this->project->getIndexes()->get('classes')->getAll(),
            $expectedElementNames,
            $expectedDescriptors
        );
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddInterfacesToIndex(): void
    {
        $expectedElementNames = ['\My\Space\Interface1', '\My\Space\Interface2'];
        $expectedDescriptors = [];
        foreach ($this->project->getFiles() as $file) {
            $descriptor = $this->faker()->interfaceDescriptor(current($expectedElementNames));
            $expectedDescriptors[] = $descriptor;

            $file->getInterfaces()->add($descriptor);
            next($expectedElementNames);
        }

        $this->fixture->execute($this->project);

        self::assertSameElements(
            $this->project->getIndexes()->get('elements')->getAll(),
            $expectedElementNames,
            $expectedDescriptors
        );
        self::assertSameElements(
            $this->project->getIndexes()->get('interfaces')->getAll(),
            $expectedElementNames,
            $expectedDescriptors
        );
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddTraitsToIndex(): void
    {
        $expectedElementNames = ['\My\Space\Trait1', '\My\Space\Trait2'];
        $expectedDescriptors = [];
        foreach ($this->project->getFiles() as $file) {
            $descriptor = $this->faker()->traitDescriptor(current($expectedElementNames));
            $expectedDescriptors[] = $descriptor;

            $file->getTraits()->add($descriptor);
            next($expectedElementNames);
        }

        $this->fixture->execute($this->project);

        self::assertSameElements(
            $this->project->getIndexes()->get('elements')->getAll(),
            $expectedElementNames,
            $expectedDescriptors
        );
        self::assertSameElements(
            $this->project->getIndexes()->get('traits')->getAll(),
            $expectedElementNames,
            $expectedDescriptors
        );
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddFunctionsToIndex(): void
    {
        $expectedElementNames = ['\function1', '\function2'];
        $expectedDescriptors = [];
        foreach ($this->project->getFiles() as $file) {
            $descriptor = $this->faker()->functionDescriptor(current($expectedElementNames));
            $expectedDescriptors[] = $descriptor;

            $file->getFunctions()->add($descriptor);
            next($expectedElementNames);
        }

        $this->fixture->execute($this->project);

        self::assertSameElements(
            $this->project->getIndexes()->get('elements')->getAll(),
            $expectedElementNames,
            $expectedDescriptors
        );
        self::assertSameElements(
            $this->project->getIndexes()->get('functions')->getAll(),
            $expectedElementNames,
            $expectedDescriptors
        );
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     */
    public function testAddConstantsToIndex(): void
    {
        $expectedElementNames = ['\CONSTANT1', '\CONSTANT2'];
        $expectedDescriptors = [];
        foreach ($this->project->getFiles() as $file) {
            $descriptor = $this->faker()->constantDescriptor(current($expectedElementNames));
            $expectedDescriptors[] = $descriptor;

            $file->getConstants()->add($descriptor);
            next($expectedElementNames);
        }

        $this->fixture->execute($this->project);

        self::assertSameElements(
            $this->project->getIndexes()->get('elements')->getAll(),
            $expectedElementNames,
            $expectedDescriptors
        );
        self::assertSameElements(
            $this->project->getIndexes()->get('constants')->getAll(),
            $expectedElementNames,
            $expectedDescriptors
        );
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getSubElements
     */
    public function testAddClassConstantsToIndex(): void
    {
        $expectedClassNames = ['\My\Space\Class1', '\My\Space\Class2'];
        $expectedMemberNames = ['\My\Space\Class1::CONSTANT', '\My\Space\Class2::CONSTANT'];
        $expectedDescriptors = [];
        foreach ($this->project->getFiles() as $file) {
            $classDescriptor = $this->faker()->classDescriptor(current($expectedClassNames));
            $memberDescriptor = $this->faker()->constantDescriptor(current($expectedMemberNames));
            $classDescriptor->getConstants()->add($memberDescriptor);
            $expectedDescriptors[] = $classDescriptor;
            $expectedDescriptors[] = $memberDescriptor;

            $file->getClasses()->add($classDescriptor);
            next($expectedClassNames);
            next($expectedMemberNames);
        }

        $this->fixture->execute($this->project);

        self::assertSameElements(
            $this->project->getIndexes()->get('elements')->getAll(),
            array_merge($expectedClassNames, $expectedMemberNames),
            $expectedDescriptors
        );

        // class constants are not indexed separately
        $elements = $this->project->getIndexes()->get('constants')->getAll();
        self::assertCount(0, $elements);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getSubElements
     */
    public function testAddPropertiesToIndex(): void
    {
        $expectedClassNames = ['\My\Space\Class1', '\My\Space\Class2'];
        $expectedMemberNames = ['\My\Space\Class1::$property', '\My\Space\Class2::$property'];
        $expectedDescriptors = [];
        foreach ($this->project->getFiles() as $file) {
            $classDescriptor = $this->faker()->classDescriptor(current($expectedClassNames));
            $memberDescriptor = $this->faker()->propertyDescriptor(current($expectedMemberNames));
            $classDescriptor->getProperties()->add($memberDescriptor);
            $expectedDescriptors[] = $classDescriptor;
            $expectedDescriptors[] = $memberDescriptor;

            $file->getClasses()->add($classDescriptor);
            next($expectedClassNames);
            next($expectedMemberNames);
        }

        $this->fixture->execute($this->project);

        self::assertSameElements(
            $this->project->getIndexes()->get('elements')->getAll(),
            array_merge($expectedClassNames, $expectedMemberNames),
            $expectedDescriptors
        );

        // class properties are not indexed separately
        self::assertNull($this->project->getIndexes()->fetch('properties'));
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::execute
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::addElementsToIndexes
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getIndexKey
     * @covers \phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getSubElements
     */
    public function testAddMethodsToIndex(): void
    {
        $expectedClassNames = ['\My\Space\Class1', '\My\Space\Class2'];
        $expectedMemberNames = ['\My\Space\Class1::method()', '\My\Space\Class2::method()'];
        $expectedDescriptors = [];
        foreach ($this->project->getFiles() as $file) {
            $classDescriptor = $this->faker()->classDescriptor(current($expectedClassNames));
            $memberDescriptor = $this->faker()->methodDescriptor(current($expectedMemberNames));
            $classDescriptor->getMethods()->add($memberDescriptor);
            $expectedDescriptors[] = $classDescriptor;
            $expectedDescriptors[] = $memberDescriptor;

            $file->getClasses()->add($classDescriptor);
            next($expectedClassNames);
            next($expectedMemberNames);
        }

        $this->fixture->execute($this->project);

        self::assertSameElements(
            $this->project->getIndexes()->get('elements')->getAll(),
            array_merge($expectedClassNames, $expectedMemberNames),
            $expectedDescriptors
        );

        // class methods are not indexed separately
        self::assertNull($this->project->getIndexes()->fetch('methods'));
    }
}
