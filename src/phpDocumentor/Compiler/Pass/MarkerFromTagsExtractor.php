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
            $todos = $element->getTags()->get('todo', array());

            /** @var TagDescriptor $todo */
            foreach ($todos as $todo) {
                $fileDescriptor = $this->getFileDescriptor($element);
                $this->addTodoMarker($fileDescriptor, $todo, $element);
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
     * @param $fileDescriptor
     * @param $todo
     * @param $element
     */
    protected function addTodoMarker($fileDescriptor, $todo, $element)
    {
        $fileDescriptor->getMarkers()->add(
            array(
                'type' => 'TODO',
                'message' => $todo->getDescription(),
                'line' => $element->getLine(),
            )
        );
    }
}
