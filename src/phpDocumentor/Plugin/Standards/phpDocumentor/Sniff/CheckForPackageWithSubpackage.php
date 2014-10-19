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

use phpDocumentor\Plugin\Standards\phpDocumentor\Constraints\Classes\HasPackageWithSubpackage;
use phpDocumentor\Plugin\Standards\AbstractSniff;

/**
 * Verifies that if a DocBlock has an `@subpackage` tag that it also has an `@package` tag.
 *
 * {@inheritDoc}
 *
 * This specific Sniff verifies that when a `@subpackage` tag is provided that there is also an `@package` tag in the
 * same DocBlock.
 */
class CheckForPackageWithSubpackage extends AbstractSniff
{
    /**
     * @inheritDoc
     */
    protected function getConstraint()
    {
        return new HasPackageWithSubpackage(array('message' => $this->getName()));
    }
}
