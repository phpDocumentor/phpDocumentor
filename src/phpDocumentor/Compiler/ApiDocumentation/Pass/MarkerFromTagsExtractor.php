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

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Compiler\ApiDocumentation\ApiDocumentationPass;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Pipeline\Attribute\Stage;
use UnexpectedValueException;

/**
 * This index builder collects all markers from tags and inserts them into the marker index.
 */
#[Stage(
    'phpdoc.pipeline.api_documentation.compile',
    9000,
    'Collect all markers embedded in tags',
)]
final class MarkerFromTagsExtractor extends ApiDocumentationPass
{
    protected function process(ApiSetDescriptor $subject): ApiSetDescriptor
    {
        /** @var DescriptorAbstract $element */
        foreach ($subject->getIndexes()->fetch('elements', new Collection()) as $element) {
            /** @var TagDescriptor[] $todos */
            $todos = $element->getTags()->fetch('todo');

            if (! $todos) {
                continue;
            }

            foreach ($todos as $todo) {
                $fileDescriptor = $this->getFileDescriptor($element);
                $this->addTodoMarkerToFile($fileDescriptor, $todo, $element->getLine());
            }
        }

        return $subject;
    }

    /**
     * Retrieves the File Descriptor from the given element.
     *
     * @throws UnexpectedValueException If the provided element does not have a file associated with it.
     */
    private function getFileDescriptor(DescriptorAbstract $element): FileDescriptor
    {
        $fileDescriptor = $element instanceof FileDescriptor
            ? $element
            : $element->getFile();

        if (! $fileDescriptor instanceof FileDescriptor) {
            throw new UnexpectedValueException('An element should always have a file associated with it');
        }

        return $fileDescriptor;
    }

    /**
     * Adds a marker with the TO DO information to the file on a given line number.
     */
    private function addTodoMarkerToFile(FileDescriptor $fileDescriptor, TagDescriptor $todo, int $lineNumber): void
    {
        $fileDescriptor->getMarkers()->add(
            [
                'type' => 'TODO',
                'message' => (string) $todo->getDescription(),
                'line' => $lineNumber,
            ],
        );
    }
}
