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

namespace phpDocumentor\Plugin\Standards\phpDocumentor\Sniff;

use phpDocumentor\Plugin\Standards\phpDocumentor\Constraints\Classes\HasSingleSubpackage;
use phpDocumentor\Plugin\Standards\AbstractSniff;

/**
 * Verifies that the DocBlock does not have more than one `@subpackage` tag.
 *
 * {@inheritDoc}
 *
 * This specific Sniff verifies if only one `@subpackage` tag is provided. The PHPDoc Standard does not deal with
 * multiple `@subpackage` tags.
 */
class CheckForDuplicateSubpackage extends AbstractSniff
{
    /**
     * @inheritDoc
     */
    protected function getConstraint()
    {
        return new HasSingleSubpackage(array('message' => $this->getName()));
    }
}
