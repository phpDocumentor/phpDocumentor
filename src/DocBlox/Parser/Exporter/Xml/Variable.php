<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Parser\Exporter\Xml
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Exports the details of a variable to XML.
 *
 * @category DocBlox
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Parser_Exporter_Xml_Variable
{
    public function export(
        DOMElement $parent, DocBlox_Reflection_Variable $variable,
        DOMElement $child = null
    ) {
        if (!$child) {
            $child = new DOMElement('variable');
            $parent->appendChild($child);
        }

        $child->setAttribute('line', $variable->getLineNumber());


        $child->appendChild(new DOMElement('name', $variable->getName()));
        $default = new DOMElement('default');
        $child->appendChild($default);
        $default->appendChild(
            $child->ownerDocument->createCDATASection($variable->getDefault())
        );

        $object = new DocBlox_Parser_Exporter_Xml_DocBlock();
        $object->export($child, $variable);
    }
}