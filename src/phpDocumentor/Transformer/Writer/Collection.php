<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @category   phpDocumentor
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

/**
 * Collection object for a set of Writers.
 *
 * @category   phpDocumentor
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Collection extends \ArrayObject
{
    /**
     * Registers a writer with a given name.
     *
     * @param string         $index a Writer's name, must be at least 3
     *     characters, alphanumeric and/or contain one or more hyphens,
     *     underscores and forward slashes.
     * @param WriterAbstract $newval The Writer object to register to this name.
     *
     * @throws \InvalidArgumentException if either of the above restrictions is
     *     not met.
     *
     * @return void
     */
    public function offsetSet($index, $newval)
    {
        if (!$newval instanceof WriterAbstract) {
            throw new \InvalidArgumentException(
                'The Writer Collection may only contain objects descending from '
                .'WriterAbstract'
            );
        }

        if (!preg_match('/^[a-zA-Z0-9\-\_\/]{3,}$/', $index)) {
            throw new \InvalidArgumentException(
                'The name of a Writer may only contain alphanumeric characters, '
                .'one or more hyphens, underscores and forward slashes and must '
                .'be at least three characters wide'
            );
        }

        parent::offsetSet($index, $newval);
    }
}
