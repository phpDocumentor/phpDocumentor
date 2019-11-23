<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2019 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 *
 *
 */

namespace phpDocumentor\Descriptor;

/**
 * Base class for descriptors containing the most used options.
 */
interface Descriptor
{
    /**
     * Returns the local name for this element.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the description for this element.
     *
     * This method will automatically attempt to inherit the parent's description if this one has none.
     *
     * @return string
     */
    public function getDescription();
}
