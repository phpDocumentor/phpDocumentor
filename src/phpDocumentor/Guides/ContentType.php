<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Guides;

/**
 * Class to define the content type of a document.
 */
final class ContentType
{
    /**
     * The actual content type.
     *
     * @var string $contentType
     */
    private $contentType;

    /**
     * Initializes the object.
     *
     * @param string $contentType
     */
    public function __construct($contentType)
    {
        $this->contentType = (string)$contentType;
    }

    /**
     * Returns a string representation of content type.
     * @return string
     */
    public function __toString()
    {
        return $this->contentType;
    }
}
