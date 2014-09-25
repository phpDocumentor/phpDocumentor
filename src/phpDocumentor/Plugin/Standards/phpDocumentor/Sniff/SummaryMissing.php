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

use Symfony\Component\Validator\Constraints as Assert;
use phpDocumentor\Plugin\Standards\AbstractSniff;

/**
 * Verifies if the DocBlock has a summary.
 *
 * {@inheritDoc}
 *
 * This specific Sniff verifies if the Summary of a DocBlock has been provided. If it is not then a Violation is
 * emitted to indicate this.
 */
class SummaryMissing extends AbstractSniff
{
    /**
     * @inheritDoc
     */
    protected $property = 'summary';

    /**
     * @inheritDoc
     */
    protected function getConstraint()
    {
        return new Assert\NotBlank(array('message' => $this->getName()));
    }
}
