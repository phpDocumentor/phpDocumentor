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

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Event\EventAbstract;

/**
 * Event that happens prior to the execution of all transformations.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
class PreTransformEvent extends EventAbstract
{
    /** @var \DOMDocument */
    protected $source;

    /**
     * Sets the source DOMDocument into this event.
     *
     * This DOMDocument contains the entire Abstract Syntax Tree and may be used
     * to extract information from or alter information in.
     *
     * @param \DOMDocument $source
     *
     * @return PreTransformEvent for a fluent interface
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Returns the Abstract Syntax Tree.
     *
     * @return \DOMDocument
     */
    public function getSource()
    {
        return $this->source;
    }
}
