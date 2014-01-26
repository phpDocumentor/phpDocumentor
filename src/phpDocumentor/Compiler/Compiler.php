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

namespace phpDocumentor\Compiler;

/**
 * Contains a series of compiler steps in a specific order; ready to be executed during transformation.
 */
class Compiler extends \SplPriorityQueue
{
    /** @var integer Default priority assigned to Compiler Passes without provided priority */
    const PRIORITY_DEFAULT = 10000;

    /**
     * {@inheritDoc}
     */
    public function insert($value, $priority = self::PRIORITY_DEFAULT)
    {
        parent::insert($value, $priority);
    }
}
