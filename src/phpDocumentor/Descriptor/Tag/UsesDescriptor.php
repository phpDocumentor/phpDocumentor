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
     *
     * @return void
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }
}
