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

use phpDocumentor\Reflection\FunctionReflector\ArgumentReflector;

/**
 * Exports an argument element into the given DOMElement.
 */
class ArgumentExporter
{

    /**
     * Exports the given reflection object to the parent XML element.
     *
     * This method creates a new child element on the given parent XML element
     * and takes the properties of the Reflection argument and sets the
     * elements and attributes on the child.
     *
     * If a child DOMElement is provided then the properties and attributes are
     * set on this but the child element is not appended onto the parent. This
     * is the responsibility of the invoker. Essentially this means that the
     * $parent argument is ignored in this case.
     *
     * @param \DOMElement       $parent   The parent element to augment.
     * @param ArgumentReflector $argument The data source.
     * @param \DOMElement       $child    Optional: child element to use instead
     *     of creating a new one on the $parent.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $argument, \DOMElement $child = null
    ) {
        if (!$child) {
            $child = new \DOMElement('argument');
            $parent->appendChild($child);
        }

        $child->setAttribute('line', $argument->getLineNumber());
        $child->appendChild(new \DOMElement('name', $argument->getName()));
        $default = new \DOMElement('default');
        $child->appendChild($default);

        /** @var \DOMDocument $dom_document */
        $dom_document = $child->ownerDocument;

        $default->appendChild(
            $dom_document->createCDATASection($argument->getDefault())
        );

        $type = $argument->getType();
        $child->appendChild(new \DOMElement('type',  $type ? $type : ''));
    }
}