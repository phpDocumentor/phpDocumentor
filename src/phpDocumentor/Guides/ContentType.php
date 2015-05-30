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
 * Class to define the content type of a document
 */
final class ContentType
{
    /**
     * @var string $contentType
     */
    private $contentType;

    /**
     * @param string $contentType
     */
    public function __construct($contentType)
    {
        $this->contentType = (string)$contentType;
    }

    public function __toString()
    {
        return $this->contentType;
    }
}
