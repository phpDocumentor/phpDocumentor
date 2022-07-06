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

use phpDocumentor\Descriptor\ApiSetDescriptorBuilder;
use phpDocumentor\Descriptor\Descriptor;

/**
 * @template TDescriptor of Descriptor
 * @template TInput of object
 */
interface AssemblerInterface
{
    //phpcs:disable
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param TInput $data
     *
     * @return TDescriptor|null
     */
    public function create(object $data);
    //phpcs:enable

    public function setBuilder(ApiSetDescriptorBuilder $builder): void;
}
