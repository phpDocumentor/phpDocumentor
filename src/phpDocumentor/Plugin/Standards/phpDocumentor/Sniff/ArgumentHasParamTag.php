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

use phpDocumentor\Plugin\Standards\phpDocumentor\Constraints\Functions\IsArgumentInDocBlock;
use phpDocumentor\Plugin\Standards\AbstractSniff;

/**
 * Checks if all arguments in a function or method signature are present as `@param` tags in the DocBlock.
 *
 * {@inheritDoc}
 *
 * This particular Sniff attempts to check whether an Argument in the function signature is also present in the
 * DocBlock in the form of an `@param` tag.
 */
class ArgumentHasParamTag extends AbstractSniff
{
    /**
     * @inheritDoc
     */
    protected function getConstraint()
    {
        return new IsArgumentInDocBlock(array('message' => $this->getName()));
    }
}
