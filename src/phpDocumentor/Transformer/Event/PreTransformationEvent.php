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
 * Event happening prior to each individual transformation.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
class PreTransformationEvent extends EventAbstract
{
    /** @var \DOMDocument */
    protected $source;

    /**
     * Sets the Abstract Syntax Tree as DOMDocument.
     *
     * @param \DOMDocument $source
     *
     * @return PreTransformationEvent
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Returns the Abstract Syntax Tree as DOMDocument.
     *
     * @return \DOMDocument
     */
    public function getSource()
    {
        return $this->source;
    }
}
