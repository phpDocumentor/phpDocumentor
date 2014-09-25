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

use phpDocumentor\Plugin\Standards\phpDocumentor\Constraints\Classes\HasSinglePackage;
use phpDocumentor\Plugin\Standards\AbstractSniff;

/**
 * Verifies that the DocBlock does not have more than one `@package` tag.
 *
 * {@inheritDoc}
 *
 * This specific Sniff verifies if only one `@package` tag is provided. The PHPDoc Standard does not deal with multiple
 * `@package` tags.
 */
class CheckForDuplicatePackage extends AbstractSniff
{
    /**
     * @inheritDoc
     */
    protected function getConstraint()
    {
        return new HasSinglePackage(array('message' => $this->getName()));
    }
}
