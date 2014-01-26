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

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\Collection;

/**
 * Interface to determine which elements can be filtered and to provide a way to set errors on the descriptor.
 */
interface Filterable
{
    /**
     * Sets a list of errors on the associated element.
     *
     * @param Collection $errors
     *
     * @return void
     */
    public function setErrors(Collection $errors);
}
