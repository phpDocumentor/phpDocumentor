<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Parser\Exporter\Xml
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Exporter\Xml;

/**
 * Exports a property definition to the given DOMElement.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class PropertyExporter
{
    /**
     * Export the given property definition to the provided parent element.
     *
     * @param \DOMElement                        $parent   Element to augment.
     * @param \phpDocumentor\Reflection\ClassReflector\PropertyReflector $property Element to export.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $property
    ) {
        $child = new \DOMElement('property');
        $parent->appendChild($child);

        $child->setAttribute('final', $property->isFinal() ? 'true' : 'false');
        $child->setAttribute('static', $property->isStatic() ? 'true' : 'false');
        $child->setAttribute('visibility', $property->getVisibility());

        $object = new VariableExporter();
        $object->export($parent, $property, $child);
    }
}