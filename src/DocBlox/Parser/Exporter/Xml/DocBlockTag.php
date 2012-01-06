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
 * Exports the information from a DocBlock's Tag.
 *
 * @category DocBlox
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Parser_Exporter_Xml_DocBlockTag
{
    public function export(
        DOMElement $parent, DocBlox_Reflection_DocBlock_Tag $tag,
        DocBlox_Reflection_DocBlockedAbstract $element
    ) {
        $child = new DOMElement('tag');
        $parent->appendChild($child);

        $child->setAttribute('line', $parent->getAttribute('line'));

        $element->dispatch(
            'reflection.docblock.tag.export',
            array(
                'object' => $tag,
                'xml' => simplexml_import_dom($child)
            )
        );
    }
}