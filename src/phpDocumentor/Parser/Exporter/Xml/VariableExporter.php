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
 * Exports the details of a variable to XML.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class VariableExporter
{
    /**
     * Export this variable definition to the given parent DOMElement.
     *
     * @param \DOMElement                        $parent   Element to augment.
     * @param \phpDocumentor\Reflection\ClassReflector\PropertyReflector $variable Element to log from.
     * @param \DOMElement                        $child    if supplied this
     *     element will be augmented instead of freshly added.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $variable,
        \DOMElement $child = null
    ) {
        if (!$child) {
            $child = new \DOMElement('variable');
            $parent->appendChild($child);
        }

        $child->setAttribute('line', $variable->getLineNumber());

        $child->setAttribute(
            'namespace',
            $variable->getNamespace()
            ? $variable->getNamespace()
            : $parent->getAttribute('namespace')
        );

        $child->appendChild(new \DOMElement('name', $variable->getName()));
        $default = new \DOMElement('default');
        $child->appendChild($default);

        /** @var \DOMDocument $dom_document */
        $dom_document = $child->ownerDocument;

        $default->appendChild(
            $dom_document->createCDATASection($variable->getDefault())
        );

        $object = new DocBlockExporter();
        $object->export($child, $variable);
    }
}