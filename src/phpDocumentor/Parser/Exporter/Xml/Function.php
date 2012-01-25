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
 *
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Parser_Exporter_Xml_Function
{
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