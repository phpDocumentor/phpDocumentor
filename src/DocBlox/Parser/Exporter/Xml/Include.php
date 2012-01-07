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
 * Exports the details of an Include element to XML.
 *
 * @category DocBlox
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Parser_Exporter_Xml_Include
{
    public function export(
        DOMElement $parent, DocBlox_Reflection_Include $include
    ) {
        $child = new DOMElement('include');
        $parent->appendChild($child);

        $child->setAttribute('line', $include->getLineNumber());
        $child->setAttribute('type', $include->getType());

        $child->appendChild(new DOMElement('name', $include->getName()));

        $object = new DocBlox_Parser_Exporter_Xml_DocBlock();
        $object->export($child, $include);
    }
}