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

/**
 * Exports a property definition to the given DOMElement.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Parser_Exporter_Xml_Property
{
    /**
     * Export the given property definition to the provided parent element.
     *
     * @param DOMElement                        $parent   Element to augment.
     * @param phpDocumentor_Reflection_Property $property Element to export.
     *
     * @return void
     */
    public function export(
        DOMElement $parent, phpDocumentor_Reflection_Property $property
    ) {
        $child = new DOMElement('property');
        $parent->appendChild($child);

        $child->setAttribute('final', $property->isFinal() ? 'true' : 'false');
        $child->setAttribute('static', $property->isStatic() ? 'true' : 'false');
        $child->setAttribute('visibility', $property->getVisibility());

        $object = new phpDocumentor_Parser_Exporter_Xml_Variable();
        $object->export($parent, $property, $child);
    }
}