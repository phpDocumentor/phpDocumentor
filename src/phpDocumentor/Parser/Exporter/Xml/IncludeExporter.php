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
 * Exports the details of an Include element to XML.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class IncludeExporter
{
    /**
     * Export the given include definition to the provided parent element.
     *
     * @param \DOMElement                       $parent  Element to augment.
     * @param \phpDocumentor\Reflection\IncludeReflector $include Element to export.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $include
    ) {
        $child = new \DOMElement('include');
        $parent->appendChild($child);

        $child->setAttribute('line', $include->getLineNumber());
        $child->setAttribute('type', $include->getType());

        $child->appendChild(new \DOMElement('name', $include->getName()));

        $object = new DocBlockExporter();
        $object->export($child, $include);
    }
}