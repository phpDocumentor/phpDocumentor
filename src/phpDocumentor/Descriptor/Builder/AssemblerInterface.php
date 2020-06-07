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
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\File;

/**
 * Interface for Assembler classes that transform data to specific Descriptor types.
 */
interface AssemblerInterface
{
    //phpcs:disable
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Element|Tag|Argument|File $data
     *
     * @return Descriptor
     */
    public function create(object $data);
    //phpcs:enable

    public function setBuilder(ProjectDescriptorBuilder $builder) : void;
}
