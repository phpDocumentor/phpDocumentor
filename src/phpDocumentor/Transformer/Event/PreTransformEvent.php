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

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Event\EventAbstract;

/**
 * Event that happens prior to the execution of all transformations.
 */
class PreTransformEvent extends EventAbstract
{
    /** @var ProjectDescriptor */
    private $project;

    /**
     * Returns the descriptor describing the project.
     *
     * @return ProjectDescriptor
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Returns the descriptor describing the project.
     *
     * @param ProjectDescriptor $project
     *
     * @return $this
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }
}
