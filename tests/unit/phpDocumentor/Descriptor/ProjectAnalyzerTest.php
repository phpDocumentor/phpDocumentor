<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Faker\Faker;

use function str_replace;

use const PHP_EOL;

/** @coversDefaultClass \phpDocumentor\Descriptor\ProjectAnalyzer */
final class ProjectAnalyzerTest extends MockeryTestCase
{
    use Faker;

    private ProjectAnalyzer $fixture;

    protected function setUp(): void
    {
        $this->fixture = new ProjectAnalyzer();
    }

    public function testIfStringOutputContainsAllCounters(): void
    {
        // Arrange
        $classDescriptor1 = $this->givenAClassWithParent(ClassDescriptor::class);
        $apiSetDescriptor = self::faker()->apiSetDescriptor();
        $this->whenDescriptorHasTheFollowingFiles(
            $apiSetDescriptor,
            [
                self::faker()->fileDescriptor(),
                self::faker()->fileDescriptor(),
            ],
        );
        $this->whenDescriptorHasTheFollowingElements(
            $apiSetDescriptor,
            [
                'ds1' => $classDescriptor1,
                'ds2' => $this->givenAClassWithParent($classDescriptor1),
                'ds3' => $this->givenAnInterfaceWithParent('123'),
            ],
        );
        $this->whenDocumentationHasTheFollowingChildrenOfRootNamespace($apiSetDescriptor, [1, 2, 3]);
        $this->fixture->analyze($apiSetDescriptor);

        $expected = <<<'TEXT'
In the Project are:
         2 files
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
    private function givenAClassWithParent($parent): ClassDescriptor
    {
        $classDescriptor1 = new ClassDescriptor();
        $classDescriptor1->setParent($parent);

        return $classDescriptor1;
    }

    private function givenAnInterfaceWithParent(string $interfaceParent): InterfaceDescriptor
    {
        $classDescriptor3 = new InterfaceDescriptor();
        $classDescriptor3->setParent(new Collection([$interfaceParent]));

        return $classDescriptor3;
    }

    /**
     * Returns a mocked ProjectDescriptor object.
     */
    private function givenAProjectMock(): m\MockInterface
    {
        return m::mock(ProjectDescriptor::class)->shouldIgnoreMissing();
    }

    /**
     * Ensures that the ProjectDescriptor contains and returns the provided files.
     */
    private function whenDescriptorHasTheFollowingFiles(ApiSetDescriptor $apiSet, array $files): void
    {
        $apiSet->setFiles(new Collection($files));
    }

    /**
     * Ensures that the ProjectDescriptor has an index 'elements' with the provided elements.
     */
    private function whenDescriptorHasTheFollowingElements(
        ApiSetDescriptor $apiSet,
        array $elements,
    ): void {
        $apiSet->getIndexes()->set('elements', new Collection($elements));
    }

    /**
     * Ensures that the ProjectDescriptor has a root namespace with the provided array as children of that namespace.
     */
    private function whenDocumentationHasTheFollowingChildrenOfRootNamespace(
        ApiSetDescriptor $apiSet,
        array $rootNamespaceChildren,
    ): void {
        $apiSet->getNamespace()->setChildren(new Collection($rootNamespaceChildren));
    }
}
