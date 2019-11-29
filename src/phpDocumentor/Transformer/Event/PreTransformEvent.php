<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
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
     */
    public function getProject() : ?ProjectDescriptor
    {
        return $this->project;
    }

    /**
     * Returns the descriptor describing the project.
     *
     * @return $this
     */
    public function setProject(ProjectDescriptor $project)
    {
        $this->project = $project;

        return $this;
    }
}
