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

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/**
 * Interface for Assembler classes that transform data to specific Descriptor types.
 */
interface AssemblerInterface
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param mixed $data
     *
     * @return DescriptorAbstract|Collection
     */
    public function create($data);

    public function setBuilder(ProjectDescriptorBuilder $builder);
}
