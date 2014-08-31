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

namespace phpDocumentor\Transformer\Event;

use phpDocumentor\Event\EventAbstract;

/**
 * Event happening prior to each individual transformation.
 */
class PreTransformationEvent extends EventAbstract
{
    /** @var \DOMDocument remembers the XML-based AST so that it can be used from the listener */
    protected $source;

    protected $transformation;

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

    /**
     * @return mixed
     */
    public function getTransformation()
    {
        return $this->transformation;
    }

    /**
     * @param mixed $transformation
     */
    public function setTransformation($transformation)
    {
        $this->transformation = $transformation;

        return $this;
    }
}
