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

namespace phpDocumentor\Parser\Exporter\Xml;

use phpDocumentor\Reflection\ClassReflector\MethodReflector;

/**
 * Exports a method definition to the given DOMElement.
 */
class MethodExporter
{
    /**
     * Export the given reflected method definition to the provided parent element.
     *
     * @param \DOMElement     $parent Element to augment.
     * @param MethodReflector $method Element to export.
     *
     * @return void
     */
    public function export(\DOMElement $parent, $method)
    {
        $child = new \DOMElement('method');
        $parent->appendChild($child);

        $child->setAttribute('final', $method->isFinal() ? 'true' : 'false');
        $child->setAttribute('abstract', $method->isAbstract() ? 'true' : 'false');
        $child->setAttribute('static', $method->isStatic() ? 'true' : 'false');
        $child->setAttribute('visibility', $method->getVisibility());

        $object = new FunctionExporter();
        $object->export($parent, $method, $child);
    }
}