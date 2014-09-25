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

use phpDocumentor\Plugin\Standards\phpDocumentor\Constraints\Property\HasSummary;
use phpDocumentor\Plugin\Standards\AbstractSniff;

/**
 * Verifies if the DocBlock for property has a summary, or the `@var` tag has a description.
 *
 * {@inheritDoc}
 *
 * This specific Sniff verifies if the Summary of a DocBlock for a property has been provided or if none is found it
 * will check the description of the `@var` tag. A property deviates from other summaries because a commonly found
 * notation is to use a single line DocBlock where the description of the `@var` serves as Summary for the property.
 */
class PropertySummaryMissing extends AbstractSniff
{
    /**
     * @inheritDoc
     */
    protected function getConstraint()
    {
        return new HasSummary(array('message' => $this->getName()));
    }
}
