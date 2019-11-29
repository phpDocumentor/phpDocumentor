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

namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Descriptor;
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
     * @return Descriptor|Collection
     */
    public function create($data);

    public function setBuilder(ProjectDescriptorBuilder $builder) : void;
}
