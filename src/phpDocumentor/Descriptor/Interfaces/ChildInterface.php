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

use phpDocumentor\Descriptor\DescriptorAbstract;

/**
 * Describes the public interface for any descriptor that is the child is another.
 */
interface ChildInterface
{
    /**
     * Returns the parent for this descriptor.
     *
     * @return DescriptorAbstract
     */
    public function getParent();

    /**
     * Sets the parent for this Descriptor.
     *
     * @param DescriptorAbstract $parent
     *
     * @return void
     */
    public function setParent($parent);
}
