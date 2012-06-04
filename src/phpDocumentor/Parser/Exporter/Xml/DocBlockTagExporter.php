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
 * Exports the information from a DocBlock's Tag.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class DocBlockTag
{
    /**
     * Export this tag to the given DocBlock.
     *
     * @param \DOMElement                                  $parent  Element to
     *     augment.
     * @param \phpDocumentor\Reflection\DocBlock\Tag       $tag     The tag to
     *     export.
     * @param \phpDocumentor\Reflection\BaseReflector $element Element to
     *     log from.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $tag, $element
    ) {
        $child = new \DOMElement('tag');
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