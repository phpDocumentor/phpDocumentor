<?php

namespace phpDocumentor\Descriptor;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Tests for the \phpDocumentor\Descriptor\ProjectAnalyzer class.
 */
class ProjectAnalyzerTest extends MockeryTestCase
{
    /** @var ProjectAnalyzer */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new ProjectAnalyzer();
    }

    /**
     * @covers \phpDocumentor\Descriptor\ProjectAnalyzer::analyze
     */
    public function testFilesAreCounted()
    {
        // Arrange
        $projectDescriptor = $this->givenAProjectMock();
        $this->whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, [1, 2, 3, 4]);
        $this->whenProjectDescriptorHasTheFollowingElements($projectDescriptor, []);
        $this->whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, []);

        // Act
        $this->fixture->analyze($projectDescriptor);

        // Assert
        $this->assertAttributeSame(4, 'fileCount', $this->fixture);
    }

    /**
     * @covers \phpDocumentor\Descriptor\ProjectAnalyzer::analyze
     */
    public function testIfTopLevelNamespacesAreCounted()
    {
        // Arrange
        $projectDescriptor = $this->givenAProjectMock();
        $this->whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, []);
        $this->whenProjectDescriptorHasTheFollowingElements($projectDescriptor, []);
        $this->whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, [1, 2, 3]);

        // Act
        $this->fixture->analyze($projectDescriptor);

        // Assert
        $this->assertAttributeSame(3, 'topLevelNamespaceCount', $this->fixture);
    }

    /**
     * @covers \phpDocumentor\Descriptor\ProjectAnalyzer::analyze
     * @covers \phpDocumentor\Descriptor\ProjectAnalyzer::findAllElements
     * @covers \phpDocumentor\Descriptor\ProjectAnalyzer::incrementUnresolvedParentCounter
     */
    public function testIfUnresolvedClassesAreCounted()
    {
        // Arrange
        $classDescriptor1 = $this->givenAClassWithParent('phpDocumentor\Descriptor\ClassDescriptor');
        $projectDescriptor = $this->givenAProjectMock();
        $this->whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, []);
        $this->whenProjectDescriptorHasTheFollowingElements(
            $projectDescriptor,
            [
                'ds1' => $classDescriptor1,
                'ds2' => $this->givenAClassWithParent($classDescriptor1),
                'ds3' => $this->givenAnInterfaceWithParent('123'),
            ]
        );
        $this->whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, []);

        // Act
        $this->fixture->analyze($projectDescriptor);

        // Assert
        $this->assertAttributeSame(1, 'unresolvedParentClassesCount', $this->fixture);
    }

    /**
     * @covers \phpDocumentor\Descriptor\ProjectAnalyzer::analyze
     * @covers \phpDocumentor\Descriptor\ProjectAnalyzer::findAllElements
     * @covers \phpDocumentor\Descriptor\ProjectAnalyzer::addElementToCounter
     */
    public function testIfVariousDescriptorTypesAreCounted()
    {
        // Arrange
        $classDescriptor1 = $this->givenAClassWithParent('phpDocumentor\Descriptor\ClassDescriptor');
        $projectDescriptor = $this->givenAProjectMock();
        $this->whenProjectDescriptorHasTheFollowingFiles($projectDescriptor, []);
        $this->whenProjectDescriptorHasTheFollowingElements(
            $projectDescriptor,
            [
                'ds1' => $classDescriptor1,
                'ds2' => $this->givenAClassWithParent($classDescriptor1),
                'ds3' => $this->givenAnInterfaceWithParent('123'),
            ]
        );
        $this->whenProjectHasTheFollowingChildrenOfRootNamespace($projectDescriptor, []);

        // Act
        $this->fixture->analyze($projectDescriptor);

        // Assert
        $this->assertAttributeSame(
            [
                'phpDocumentor\Descriptor\ClassDescriptor' => 2,
                'phpDocumentor\Descriptor\InterfaceDescriptor' => 1,
            ],
            'descriptorCountByType',
            $this->fixture
        );
    }

    /**
     * @covers \phpDocumentor\Descriptor\ProjectAnalyzer::__toString
     */
    public function testIfStringOutputContainsAllCounters()
    {
        // Arrange
        $classDescriptor1 = $this->givenAClassWithParent('phpDocumentor\Descriptor\ClassDescriptor');
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
    protected function givenAClassWithParent($parent): ClassDescriptor
    {
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setParent($parent);
        return $classDescriptor1;
    }

    /**
     * @param string $interfaceParent
     */
    protected function givenAnInterfaceWithParent($interfaceParent): InterfaceDescriptor
    {
        $classDescriptor3 = new InterfaceDescriptor();
        $classDescriptor3->setParent($interfaceParent);

        return $classDescriptor3;
    }

    /**
     * Returns a mocked ProjectDescriptor object.
     */
    protected function givenAProjectMock(): m\MockInterface
    {
        return m::mock('phpDocumentor\Descriptor\ProjectDescriptor')->shouldIgnoreMissing();
    }

    /**
     * Ensures that the ProjectDescriptor contains and returns the provided files.
     */
    protected function whenProjectDescriptorHasTheFollowingFiles(m\MockInterface $projectDescriptor, array $files)
    {
        $projectDescriptor->shouldReceive('getFiles')->andReturn($files);
    }

    /**
     * Ensures that the ProjectDescriptor has an index 'elements' with the provided elements.
     */
    protected function whenProjectDescriptorHasTheFollowingElements(m\MockInterface $projectDescriptor, array $elements)
    {
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
    ) {
        $projectDescriptor->shouldReceive('getNamespace->getChildren')->andReturn(
            new Collection($rootNamespaceChildren)
        );
    }
}
