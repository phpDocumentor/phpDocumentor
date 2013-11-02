<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\DescriptorAbstract;
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
        foreach ($project->getIndexes()->get('elements') as $element) {
            $todos = $element->getTags()->get('todo', array());

            /** @var TagDescriptor $todo */
            foreach ($todos as $todo) {
                $element->getFile()->getMarkers()->add(
                    array(
                        'type'    => 'TODO',
                        'message' => $todo->getDescription(),
                        'line'    => $element->getLine(),
                    )
                );
            }
        }
    }
}
