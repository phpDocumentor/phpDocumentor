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
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Location;
use PHPUnit\Framework\TestCase;

final class MarkerFromTagsExtractorTest extends TestCase
{
    /** @var MarkerFromTagsExtractor */
    private $fixture;

    /** @var ProjectDescriptor */
    private $project;

    /**
     * Initialize the fixture for this test.
     */
    protected function setUp(): void
    {
        $this->fixture = new MarkerFromTagsExtractor();
        $this->project = new ProjectDescriptor('MyProject');
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor::getDescription
     */
    public function testDescriptionReturnsCorrectString(): void
    {
        $this->assertSame('Collect all markers embedded in tags', $this->fixture->getDescription());
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor::execute
     * @covers \phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor::getFileDescriptor
     * @covers \phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor::addTodoMarkerToFile
     */
    public function testAddTodoMarkerForEachTodoTagInAnyElement(): void
    {
        $fileDescriptor = $this->givenProjectHasFileDescriptor();
        $fileDescriptor->setStartLocation(new Location(10));
        $this->givenDescriptorHasTodoTagWithDescription($fileDescriptor, '123');
        $this->givenDescriptorHasTodoTagWithDescription($fileDescriptor, '456');
        $classDescriptor = $this->givenProjectHasClassDescriptorAssociatedWithFile($fileDescriptor);
        $classDescriptor->setStartLocation(new Location(20));
        $this->givenDescriptorHasTodoTagWithDescription($classDescriptor, '789');

        $this->fixture->execute($this->project);

        $this->assertCount(2, $fileDescriptor->getTags()->get('todo'));
        $this->assertCount(1, $classDescriptor->getTags()->get('todo'));
        $this->assertCount(3, $fileDescriptor->getMarkers());
        $this->assertSame(
            ['type' => 'TODO', 'message' => '123', 'line' => 10],
            $fileDescriptor->getMarkers()->get(0)
        );
        $this->assertSame(
            ['type' => 'TODO', 'message' => '456', 'line' => 10],
            $fileDescriptor->getMarkers()->get(1)
        );
        $this->assertSame(
            ['type' => 'TODO', 'message' => '789', 'line' => 20],
            $fileDescriptor->getMarkers()->get(2)
        );
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor::execute
     * @covers \phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor::getFileDescriptor
     */
    public function testExceptionShouldBeThrownIfElementHasNoFileAssociated(): void
    {
        $classDescriptor = $this->givenProjectHasClassDescriptorAssociatedWithFile(null);
        $this->givenDescriptorHasTodoTagWithDescription($classDescriptor, '789');

        $this->expectException('UnexpectedValueException');
        $this->expectExceptionMessage('An element should always have a file associated with it');

        $this->fixture->execute($this->project);
    }

    protected function givenProjectHasFileDescriptor(): FileDescriptor
    {
        $fileDescriptor1 = new FileDescriptor('123');
        $elementIndex = $this->project->getIndexes()->fetch('elements', new Collection());
        $elementIndex->add($fileDescriptor1);

        return $fileDescriptor1;
    }

    protected function givenDescriptorHasTodoTagWithDescription(
        DescriptorAbstract $descriptor,
        string $description
    ): void {
        $todoTag = new TagDescriptor('todo');
        $todoTag->setDescription(new DescriptionDescriptor(new Description($description), []));

        $todoTags = $descriptor->getTags()->fetch('todo', []);
        $todoTags[] = $todoTag;
        $descriptor->getTags()->set('todo', $todoTags);
    }

    /**
     * Adds a class descriptor to the project's elements and add a parent file.
     */
    private function givenProjectHasClassDescriptorAssociatedWithFile(
        ?FileDescriptor $fileDescriptor
    ): ClassDescriptor {
        $classDescriptor = new ClassDescriptor();
        if ($fileDescriptor) {
            $classDescriptor->setFile($fileDescriptor);
        }

        $elementIndex = $this->project->getIndexes()->fetch('elements', new Collection());
        $elementIndex->add($classDescriptor);

        return $classDescriptor;
    }
}
