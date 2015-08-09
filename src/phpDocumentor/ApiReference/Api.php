<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\ApiReference;

use phpDocumentor\DocumentGroupFormat;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;

/**
 * Container for Elements.
 */
final class Api
{
    /**
     * Format of the api.
     *
     * @var DocumentGroupFormat
     */
    private $format;

    /**
     * Collection of elements in the api.
     *
     * @var Element[]
     */
    private $elements = [];

    /**
     * Initialized the class with the given format.
     *
     * @param DocumentGroupFormat $format
     */
    public function __construct(DocumentGroupFormat $format)
    {
        $this->format = $format;
    }

    /**
     * Returns the format of this API.
     *
     * @return DocumentGroupFormat
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Will return the Element when it is available. Otherwise returns null.
     *
     * @param Fqsen $fqsen
     *
     * @return Element|null
     */
    public function findElementByFqsen(Fqsen $fqsen)
    {
        if (isset($this->elements[(string)$fqsen])) {
            return $this->elements[(string)$fqsen];
        }

        return null;
    }

    /**
     * Add an element to the api.
     *
     * @param Element $element
     *
     * @return void
     */
    public function addElement(Element $element)
    {
        $this->elements[(string) $element->getFqsen()] = $element;
    }

    /**
     * Returns all elements of the api.
     *
     * @return Element[]
     */
    public function getElements()
    {
        return $this->elements;
    }
}