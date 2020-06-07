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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\NamespaceDescriptor;

/**
 * Describes the public interface for the description of a namespace.
 */
interface NamespaceInterface extends ElementInterface, ContainerInterface, ChildInterface
{
    /**
     * @return Collection<NamespaceDescriptor>
     */
    public function getChildren() : Collection;
}
