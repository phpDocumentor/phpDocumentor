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

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;

/**
 * Descriptor representing the uses tag on any element.
 */
class UsesDescriptor extends TagDescriptor
{
    /** @var string the FQSEN where the uses tag refers to */
    protected $reference = '';

    /**
     * Returns the FQSEN to which this tag points.
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Sets the FQSEN to which this tag points.
     *
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }
}
