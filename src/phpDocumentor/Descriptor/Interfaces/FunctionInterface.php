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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Collection;

/**
 * Descriptor representing a global function in a file.
 */
interface FunctionInterface extends ElementInterface, TypeInterface
{
    /**
     * Sets the arguments related to this function.
     *
     * @param Collection $arguments
     *
     * @return void
     */
    public function setArguments(Collection $arguments);

    /**
     * Returns the arguments related to this function.
     *
     * @return Collection
     */
    public function getArguments();
}
