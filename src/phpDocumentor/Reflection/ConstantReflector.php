<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

/**
 * Provides Static Reflection for file-level constants.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class ConstantReflector extends BaseReflector
{
    /** @var \PHPParser_Node_Const */
    protected $node;

    /**
     * Returns the value contained in this Constant.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->getRepresentationOfValue($this->node->value);
    }
}
