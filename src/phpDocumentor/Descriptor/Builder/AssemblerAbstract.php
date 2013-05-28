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

namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\Builder\AssemblerInterface;
use phpDocumentor\Descriptor\Builder;

/**
 * Base class for all assemblers.
 */
abstract class AssemblerAbstract implements AssemblerInterface
{
    /**
     * Registers the Builder with this Assembler.
     *
     * The Builder may be used to recursively assemble Descriptors using
     * the {@link Builder::buildDescriptor()} method.
     *
     * @param Builder $builder
     *
     * @return void
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;
    }
}
