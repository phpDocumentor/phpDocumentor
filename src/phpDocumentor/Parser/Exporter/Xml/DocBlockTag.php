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
 * Exports the information from a DocBlock's Tag.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Parser_Exporter_Xml_DocBlockTag
{
    /**
     * Export this tag to the given DocBlock.
     *
     * @param DOMElement                                  $parent  Element to
     *     augment.
     * @param phpDocumentor_Reflection_DocBlock_Tag       $tag     The tag to
     *     export.
     * @param phpDocumentor_Reflection_DocBlockedAbstract $element Element to
     *     log from.
     *
     * @return void
     */
    public function export(
        DOMElement $parent, phpDocumentor_Reflection_DocBlock_Tag $tag,
        phpDocumentor_Reflection_DocBlockedAbstract $element
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