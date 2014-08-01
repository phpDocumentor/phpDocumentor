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

namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Base class for all assemblers.
 */
abstract class AssemblerAbstract implements AssemblerInterface
{
    /** @var ProjectDescriptorBuilder|null $builder */
    protected $builder;

    /**
     * Returns the builder for this Assembler or null if none is set.
     *
     * @return null|ProjectDescriptorBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Registers the Builder with this Assembler.
     *
     * The Builder may be used to recursively assemble Descriptors using
     * the {@link ProjectDescriptorBuilder::buildDescriptor()} method.
     *
     * @param ProjectDescriptorBuilder $builder
     *
     * @return void
     */
    public function setBuilder(ProjectDescriptorBuilder $builder)
    {
        $this->builder = $builder;
    }
}
