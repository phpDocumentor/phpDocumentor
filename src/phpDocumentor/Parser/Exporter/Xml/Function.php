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
 * Exports the given reflected file to a DOMElement.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Parser_Exporter_Xml_Function
{
    /**
     * Export this function definition to the given parent DOMElement.
     *
     * @param DOMElement                        $parent   Element to augment.
     * @param phpDocumentor_Reflection_Function $function Element to export.
     * @param DOMElement                        $child    if supplied this element
     *     will be augmented instead of freshly added.
     *
     * @return void
     */
    public function export(
        DOMElement $parent, phpDocumentor_Reflection_Function $function,
        DOMElement $child = null
    ) {
        if (!$child) {
            $child = new DOMElement('function');
            $parent->appendChild($child);
        }

        $child->setAttribute('namespace', $function->getNamespace());
        $child->setAttribute('line', $function->getLineNumber());

        $child->appendChild(new DOMElement('name', $function->getName()));
        $child->appendChild(new DOMElement('type', $function->getType()));

        $object = new phpDocumentor_Parser_Exporter_Xml_DocBlock();
        $function->setDefaultPackageName($parent->getAttribute('package'));
        $object->export($child, $function);

        foreach ($function->getArguments() as $argument) {
            $object = new phpDocumentor_Parser_Exporter_Xml_Argument();
            $object->export($child, $argument);
        }
    }
}