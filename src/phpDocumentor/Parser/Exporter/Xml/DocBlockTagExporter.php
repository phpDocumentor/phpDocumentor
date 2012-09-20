<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Exporter\Xml;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\BaseReflector;

/**
 * Exports the information from a DocBlock's Tag.
 */
class DocBlockTagExporter
{
    /**
     * Export this tag to the given DocBlock.
     *
     * This method also invokes the 'reflection.docblock.tag.export' which can
     * be used to augment the data. This is useful for plugins so that they
     * can provide custom tags.
     *
     * @param \DOMElement   $parent  Element to augment.
     * @param Tag           $tag     The tag to export.
     * @param BaseReflector $element Element to log from.
     *
     * @return void
     */
    public function export(\DOMElement $parent, $tag, $element)
    {
        $child = new \DOMElement('tag');
        $parent->appendChild($child);

        $child->setAttribute('line', $parent->getAttribute('line'));

        if (class_exists('phpDocumentor\Event\Dispatcher')) {
            \phpDocumentor\Event\Dispatcher::getInstance()->dispatch(
                'reflection.docblock.tag.export',
                \phpDocumentor\Reflection\Event\ExportDocBlockTagEvent
                ::createInstance($element)->setObject($tag)
                    ->setXml(simplexml_import_dom($child))
            );
        }
    }
}