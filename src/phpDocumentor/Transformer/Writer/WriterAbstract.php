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

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;
use Stringable;

/**
 * Base class for the actual transformation business logic (writers).
 */
abstract class WriterAbstract implements Stringable
{
    /**
     * Name of this writer, used to identify this writer.
     *
     * This name is also used in {@see \phpDocumentor\Transformer\Writer\Collection} as key and should match the
     * name provided in the template definitions' transformations.
     */
    abstract public function getName(): string;

    /**
     * This method verifies whether PHP has all requirements needed to run this writer.
     *
     * If one of the requirements is missing for this Writer then an exception of type RequirementMissing
     * should be thrown; this indicates to the calling process that this writer will not function.
     *
     * @throws Exception\RequirementMissing When a requirements is missing stating which one.
     *
     * @codeCoverageIgnore
     */
    public function checkRequirements(): void
    {
        // empty body since most writers do not have requirements
    }

    /**
     * Abstract definition of the transformation method.
     *
     * @param Transformation $transformation Transformation to execute.
     * @param ProjectDescriptor $project        Document containing the structure.
     */
    abstract public function transform(
        Transformation $transformation,
        ProjectDescriptor $project,
        DocumentationSetDescriptor $documentationSet,
    ): void;

    public function __toString(): string
    {
        return static::class;
    }
}
