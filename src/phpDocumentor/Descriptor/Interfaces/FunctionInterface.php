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
 * Descriptor representing a global function in a file.
 */
interface FunctionInterface extends ElementInterface, TypeInterface
{
    /**
     * Sets the arguments related to this function.
     */
    public function setArguments(Collection $arguments) : void;

    /**
     * Returns the arguments related to this function.
     */
    public function getArguments() : Collection;
}
