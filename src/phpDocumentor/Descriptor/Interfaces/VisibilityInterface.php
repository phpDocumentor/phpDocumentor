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

interface VisibilityInterface
{
    /**
     * Returns the visibility for this element.
     *
     * The following values are supported:
     *
     * - public
     * - protected
     * - private
     *
     * @return string
     */
    public function getVisibility();
}
