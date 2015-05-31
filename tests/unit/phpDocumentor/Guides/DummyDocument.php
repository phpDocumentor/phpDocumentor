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
 * Dummy class implementing Document
 */
final class DummyDocument extends Document
{
    /**
     * Returns the type of the document.
     *
     * @return ContentType
     */
    public function getContentType()
    {
        return null;
    }
}