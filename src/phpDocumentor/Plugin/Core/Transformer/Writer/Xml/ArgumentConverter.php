<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer\Xml;

use phpDocumentor\Descriptor\ArgumentDescriptor;

class ArgumentConverter
{
    /**
     * Exports the given reflection object to the parent XML element.
     *
     * This method creates a new child element on the given parent XML element
     * and takes the properties of the Reflection argument and sets the
     * elements and attributes on the child.
     *
     * @param \DOMElement        $parent   The parent element to augment.
     * @param ArgumentDescriptor $argument The data source.
     *
     * @return \DOMElement
     */
    public function convert(\DOMElement $parent, ArgumentDescriptor $argument)
    {
        $child = new \DOMElement('argument');
        $parent->appendChild($child);

        $child->setAttribute('line', $argument->getLine());
        $child->setAttribute('by_reference', var_export($argument->isByReference(), true));
        $child->appendChild(new \DOMElement('name', $argument->getName()));
        $child->appendChild(new \DOMElement('default'))
              ->appendChild(new \DOMText($argument->getDefault()));

        $types = $argument->getTypes();
        $child->appendChild(new \DOMElement('type', implode('|', $types)));

        return $child;
    }
}
