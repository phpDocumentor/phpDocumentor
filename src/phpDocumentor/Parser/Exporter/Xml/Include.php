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
 * Exports the details of an Include element to XML.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Parser_Exporter_Xml_Include
{
    /**
     * Export the given include definition to the provided parent element.
     *
     * @param DOMElement                       $parent  Element to augment.
     * @param phpDocumentor_Reflection_Include $include Element to export.
     *
     * @return void
     */
    public function export(
        DOMElement $parent, phpDocumentor_Reflection_Include $include
    ) {
        $child = new DOMElement('include');
        $parent->appendChild($child);

        $child->setAttribute('line', $include->getLineNumber());
        $child->setAttribute('type', $include->getType());

        $child->appendChild(new DOMElement('name', $include->getName()));

        $object = new phpDocumentor_Parser_Exporter_Xml_DocBlock();
        $object->export($child, $include);
    }
}