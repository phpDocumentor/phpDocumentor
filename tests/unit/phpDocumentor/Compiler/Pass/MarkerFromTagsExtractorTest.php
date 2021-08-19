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

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Location;
use PHPUnit\Framework\TestCase;

final class MarkerFromTagsExtractorTest extends TestCase
{
    use Faker;

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
        $this->project = $this->faker()->apiSetDescriptor();
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor::getDescription
     */
    public function testDescriptionReturnsCorrectString(): void
    {
        self::assertSame('Collect all markers embedded in tags', $this->fixture->getDescription());
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor::execute
     * @covers \phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor::getFileDescriptor
     * @covers \phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor::addTodoMarkerToFile
     */
    public function testAddTodoMarkerForEachTodoTagInAnyElement(): void
    {
        $fileDescriptor = $this->faker()->fileDescriptor();
        $fileDescriptor->setStartLocation(new Location(10));
        $this->givenDescriptorHasTodoTagWithDescription($fileDescriptor, '123');
        $this->givenDescriptorHasTodoTagWithDescription($fileDescriptor, '456');
        $classDescriptor = $this->faker()->classDescriptor();
        $classDescriptor->setFile($fileDescriptor);
        $classDescriptor->setStartLocation(new Location(20));
        $this->givenDescriptorHasTodoTagWithDescription($classDescriptor, '789');
        $this->project->addFile($fileDescriptor);
        $this->project->getIndexes()->fetch('elements', new Collection())->add($fileDescriptor);
        $this->project->getIndexes()->fetch('elements', new Collection())->add($classDescriptor);

        $this->fixture->execute($this->project);

        self::assertCount(2, $fileDescriptor->getTags()->get('todo'));
        self::assertCount(1, $classDescriptor->getTags()->get('todo'));
        self::assertCount(3, $fileDescriptor->getMarkers());
        self::assertSame(
            ['type' => 'TODO', 'message' => '123', 'line' => 10],
            $fileDescriptor->getMarkers()->get(0)
        );
        self::assertSame(
            ['type' => 'TODO', 'message' => '456', 'line' => 10],
            $fileDescriptor->getMarkers()->get(1)
        );
        self::assertSame(
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
        $classDescriptor = $this->faker()->classDescriptor();
        $this->project->getIndexes()->fetch('elements', new Collection())->add($classDescriptor);
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
}
