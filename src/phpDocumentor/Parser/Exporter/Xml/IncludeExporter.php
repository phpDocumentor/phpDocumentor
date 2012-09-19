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

use \phpDocumentor\Reflection\IncludeReflector;

/**
 * Exports the details of an Include element to XML.
 */
class IncludeExporter
{
    /**
     * Export the given include definition to the provided parent element.
     *
     * @param \DOMElement      $parent  Element to augment.
     * @param IncludeReflector $include Element to export.
     *
     * @return void
     */
    public function export(\DOMElement $parent, $include)
    {
        $child = new \DOMElement('include');
        $parent->appendChild($child);

        $child->setAttribute('line', $include->getLineNumber());
        $child->setAttribute('type', $include->getType());

        $child->appendChild(new \DOMElement('name', $include->getName()));

        $object = new DocBlockExporter();
        $object->export($child, $include);
    }
}