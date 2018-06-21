<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
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
     */
    public function setParent($parent);
}
