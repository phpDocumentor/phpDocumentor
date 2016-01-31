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

namespace phpDocumentor\DomainModel\Documentation;

use phpDocumentor\DomainModel\Documentation\DocumentGroup\DocumentGroupFormat;

/**
 * Interface for document groups
 */
interface DocumentGroup
{
    /**
     * Returns format of this group.
     *
     * @return DocumentGroupFormat
     */
    public function getFormat();
}
