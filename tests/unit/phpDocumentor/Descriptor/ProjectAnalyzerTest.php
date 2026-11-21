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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\ApiDocumentationSet;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function str_replace;

use const PHP_EOL;

#[CoversClass(ProjectAnalyzer::class)]
final class ProjectAnalyzerTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

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
    private function givenAProjectMock(): ProjectDescriptor
    {
        return $this->prophesize(ProjectDescriptor::class)->reveal();
    }

    /**
     * Ensures that the ProjectDescriptor contains and returns the provided files.
     */
    private function whenDescriptorHasTheFollowingFiles(ApiDocumentationSet $apiSet, array $files): void
    {
        $apiSet->setFiles(new Collection($files));
    }

    /**
     * Ensures that the ProjectDescriptor has an index 'elements' with the provided elements.
     */
    private function whenDescriptorHasTheFollowingElements(
        ApiDocumentationSet $apiSet,
        array $elements,
    ): void {
        $apiSet->getIndexes()->set('elements', new Collection($elements));
    }

    /**
     * Ensures that the ProjectDescriptor has a root namespace with the provided array as children of that namespace.
     */
    private function whenDocumentationHasTheFollowingChildrenOfRootNamespace(
        ApiDocumentationSet $apiSet,
        array $rootNamespaceChildren,
    ): void {
        $apiSet->getNamespace()->setChildren(new Collection($rootNamespaceChildren));
    }
}
