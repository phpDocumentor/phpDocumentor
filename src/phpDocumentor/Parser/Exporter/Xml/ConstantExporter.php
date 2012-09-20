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

use phpDocumentor\Reflection\ConstantReflector;

/**
 * Exports a constant element's attributes and properties to a child of the given
 * parent.
 */
class ConstantExporter
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
     * @param ConstantReflector $constant The data source.
     * @param \DOMElement       $child    Optional: child element to use
     *     instead of creating a new one on the $parent.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $constant,
        \DOMElement $child = null
    ) {
        if (!$constant->getName()) {
            return;
        }

        if (!$child) {
            $child = new \DOMElement('constant');
            $parent->appendChild($child);
        }

        $child->setAttribute(
            'namespace',
            $constant->getNamespace()
            ? $constant->getNamespace()
            : $parent->getAttribute('namespace')
        );
        $child->setAttribute('line', $constant->getLineNumber());

        $short_name = method_exists($constant, 'getShortName')
            ? $constant->getShortName() : $constant->getName();

        $child->appendChild(new \DOMElement('name', $short_name));
        $child->appendChild(
            new \DOMElement('full_name', $constant->getName())
        );

        $value = new \DOMElement('value');
        $child->appendChild($value);

        /** @var \DOMDocument $dom_document */
        $dom_document = $child->ownerDocument;

        $value->appendChild(
            $dom_document->createCDATASection($constant->getValue())
        );

        $object = new DocBlockExporter();
        $constant->setDefaultPackageName($parent->getAttribute('package'));
        $object->export($child, $constant);
    }
}