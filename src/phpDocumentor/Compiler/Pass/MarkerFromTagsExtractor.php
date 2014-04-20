<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * This index builder collects all markers from tags and inserts them into the marker index.
 */
class MarkerFromTagsExtractor implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 9000;

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Collect all markers embedded in tags';
    }

    /**
     * {@inheritDoc}
     */
    public function execute(ProjectDescriptor $project)
    {
        /** @var DescriptorAbstract $element */
        foreach ($project->getIndexes()->get('elements', new Collection()) as $element) {
            $todos = $element->getTags()->get('todo');

            if (!$todos) {
                continue;
            }

            /** @var TagDescriptor $todo */
            foreach ($todos as $todo) {
                $fileDescriptor = $this->getFileDescriptor($element);
                $this->addTodoMarkerToFile($fileDescriptor, $todo, $element->getLine());
            }
        }
    }

    /**
     * Retrieves the File Descriptor from the given element.
     *
     * @param DescriptorAbstract $element
     *
     * @throws \UnexpectedValueException if the provided element does not have a file associated with it.
     *
     * @return FileDescriptor
     */
    protected function getFileDescriptor($element)
    {
        $fileDescriptor = $element instanceof FileDescriptor
            ? $element
            : $element->getFile();

        if (!$fileDescriptor instanceof FileDescriptor) {
            throw new \UnexpectedValueException('An element should always have a file associated with it');
        }

        return $fileDescriptor;
    }

    /**
     * Adds a marker with the TO DO information to the file on a given line number.
     *
     * @param FileDescriptor $fileDescriptor
     * @param TagDescriptor  $todo
     * @param integer        $lineNumber
     *
     * @return void
     */
    protected function addTodoMarkerToFile($fileDescriptor, $todo, $lineNumber)
    {
        $fileDescriptor->getMarkers()->add(
            array(
                'type'    => 'TODO',
                'message' => $todo->getDescription(),
                'line'    => $lineNumber,
            )
        );
    }
}
