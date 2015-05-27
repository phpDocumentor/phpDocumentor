<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

/**
 * Object describing the format of a DocumentGroup
 */
final class DocumentGuideFormat
{
    /**
     * format name.
     *
     * @var string
     */
    private $format;

    /**
     * Initializes the object with the given format.
     *
     * @param string $format
     */
    public function __construct($format)
    {
        $this->format = $format;
    }

    /**
     * returns the format this object is representing.
     *
     * @return string
     */
    public function getFormat()
    {
        return (string)$this->format;
    }

    /**
     * returns a string representation of this object.
     *
     * @return string
     */
    function __toString()
    {
        return $this->getFormat();
    }
}
