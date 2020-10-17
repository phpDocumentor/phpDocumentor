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

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Event\EventAbstract;

/**
 * Event happen right after all transformations have completed.
 */
final class PostTransformEvent extends EventAbstract
{
    /** @var ProjectDescriptor|null */
    private $project;

    /**
     * Creates a new instance of a derived object and return that.
     *
     * Used as convenience method for fluent interfaces.
     */
    public static function createInstance(object $subject) : EventAbstract
    {
        return new self($subject);
    }

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
    public function setProject(ProjectDescriptor $project) : self
    {
        $this->project = $project;

        return $this;
    }
}
