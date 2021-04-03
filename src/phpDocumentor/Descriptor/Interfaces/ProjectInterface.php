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
use phpDocumentor\Descriptor\FileDescriptor;

/**
 * Describes the public interface for the description of a project.
 */
interface ProjectInterface
{
    public function setName(string $name) : void;

    public function getName() : string;
}
