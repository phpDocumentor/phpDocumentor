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

namespace phpDocumentor\DomainModel\Parser\Documentation;

use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup;

/**
 * Dummy class for test purpose only
 */
class DummyDocumentGroup implements DocumentGroup
{

    /**
     * Returns format of this group.
     *
     * @return DocumentGroup\DocumentGroupFormat
     */
    public function getFormat()
    {
        return null;
    }
}
