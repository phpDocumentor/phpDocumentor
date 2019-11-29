<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\Collection as DescriptorCollection;
use const PHP_EOL;
use function str_replace;

/**
 * Tests for the \phpDocumentor\Descriptor\ProjectAnalyzer class.
 */
class ProjectAnalyzerTest extends MockeryTestCase
{
    /** @var ProjectAnalyzer */
    private $fixture;

    protected function setUp() : void
    {
        $this->fixture = new ProjectAnalyzer();
    }

    /**
     * @covers \phpDocumentor\Descriptor\ProjectAnalyzer::__toString
     */
    public function testIfStringOutputContainsAllCounters() : void
    {
        // Arrange
        $classDescriptor1  = $this->givenAClassWithParent('phpDocumentor\Descriptor\ClassDescriptor');
        $projectDescriptor = $this->givenAProjectMock();
        $this->whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, [1, 2, 3, 4]);
        $this->whenProjectDescriptorHasTheFollowingElements(
            $projectDescriptor,
            [
                'ds1' => $classDescriptor1,
                'ds2' => $this->givenAClassWithParent($classDescriptor1),
                'ds3' => $this->givenAnInterfaceWithParent('123'),
            ]
        );
        $this->whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, [1, 2, 3]);
        $this->fixture->analyze($projectDescriptor);

        $expected = <<<TEXT
In the ProjectDescriptor are:
         4 files
         3 top-level namespaces
         1 unresolvable parent classes
         2 phpDocumentor\Descriptor\ClassDescriptor elements
         1 phpDocumentor\Descriptor\InterfaceDescriptor elements

TEXT;
        $expected = str_replace("\n", PHP_EOL, $expected);

        // Act
        $result = (string) $this->fixture;

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * Returns a class with the given parent set.
     *
     * @param string|DescriptorAbstract $parent
     */
    protected function givenAClassWithParent($parent) : ClassDescriptor
    {
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setParent($parent);
        return $classDescriptor1;
    }

    protected function givenAnInterfaceWithParent(string $interfaceParent) : InterfaceDescriptor
    {
        $classDescriptor3 = new InterfaceDescriptor();
        $classDescriptor3->setParent(new Collection([$interfaceParent]));

        return $classDescriptor3;
    }

    /**
     * Returns a mocked ProjectDescriptor object.
     */
    protected function givenAProjectMock() : m\MockInterface
    {
        return m::mock('phpDocumentor\Descriptor\ProjectDescriptor')->shouldIgnoreMissing();
    }

    /**
     * Ensures that the ProjectDescriptor contains and returns the provided files.
     */
    protected function whenProjectDescriptorHasTheFollowingFiles(
        m\MockInterface $projectDescriptor,
        array $files
    ) : void {
        $projectDescriptor->shouldReceive('getFiles')->andReturn(new DescriptorCollection($files));
    }

    /**
     * Ensures that the ProjectDescriptor has an index 'elements' with the provided elements.
     */
    protected function whenProjectDescriptorHasTheFollowingElements(
        m\MockInterface $projectDescriptor,
        array $elements
    ) : void {
        $projectDescriptor->shouldReceive('getIndexes->get')
            ->with('elements', m::type('phpDocumentor\Descriptor\Collection'))
            ->andReturn(new Collection($elements));
    }

    /**
     * Ensures that the ProjectDescriptor has a root namespace with the provided array as children of that namespace.
     */
    protected function whenProjectHasTheFollowingChildrenOfRootNamespace(
        m\MockInterface $projectDescriptor,
        array $rootNamespaceChildren
    ) : void {
        $projectDescriptor->shouldReceive('getNamespace->getChildren')->andReturn(
            new Collection($rootNamespaceChildren)
        );
    }
}
