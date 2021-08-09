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

namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Base class for all assemblers.
 *
 * @template TDescriptor of Descriptor
 * @template TInput of object
 * @implements AssemblerInterface<TDescriptor, TInput>
 */
abstract class AssemblerAbstract implements AssemblerInterface
{
    /** @var ProjectDescriptorBuilder|null $builder */
    protected $builder;

    /**
     * Returns the builder for this Assembler or null if none is set.
     */
    public function getBuilder(): ?ProjectDescriptorBuilder
    {
        return $this->builder;
    }

    /**
     * Registers the Builder with this Assembler.
     *
     * The Builder may be used to recursively assemble Descriptors using
     * the {@link ProjectDescriptorBuilder::buildDescriptor()} method.
     */
    public function setBuilder(ProjectDescriptorBuilder $builder): void
    {
        $this->builder = $builder;
    }
}
