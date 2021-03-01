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

use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Event\EventAbstract;
use phpDocumentor\Transformer\Transformation;

/**
 * Event that happens prior to the execution of all transformations.
 */
final class PreTransformEvent extends EventAbstract
{
    /** @var DocumentationSetDescriptor|null */
    private $documentationSetDescriptor;

    /** @var Transformation[] */
    private $transformations;

    /**
     * Creates a new instance of a derived object and return that.
     *
     * Used as convenience method for fluent interfaces.
     */
    public static function createInstance(object $subject): EventAbstract
    {
        return new self($subject);
    }

    /**
     * Returns the descriptor describing the project.
     */
    public function getDocumentationSetDescriptor(): ?DocumentationSetDescriptor
    {
        return $this->documentationSetDescriptor;
    }

    /**
     * Returns the descriptor describing the project.
     *
     * @return $this
     */
    public function setDocumentationSet(DocumentationSetDescriptor $documentationSetDescriptor): self
    {
        $this->documentationSetDescriptor = $documentationSetDescriptor;

        return $this;
    }

    /** @param Transformation[] $transformations */
    public function setTransformations(array $transformations): void
    {
        $this->transformations = $transformations;
    }

    /** @return Transformation[] */
    public function getTransformations(): array
    {
        return $this->transformations;
    }
}
