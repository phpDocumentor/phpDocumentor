<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

/**
 * Interface representing the common interface for all elements that can contain sub-elements.
 */
interface ContainerInterface
{
    public function getConstants() : Collection;

    public function getFunctions() : Collection;

    public function getClasses() : Collection;

    public function getInterfaces() : Collection;

    public function getTraits() : Collection;
}
