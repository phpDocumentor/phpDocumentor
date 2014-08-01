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

use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;

/**
 * Converter used to create an XML Element representing the property and its DocBlock.
 *
 * In order to convert the DocBlock to its XML representation this class requires the respective converter.
 */
class PropertyConverter
{
    /** @var DocBlockConverter */
    protected $docBlockConverter;

    /**
     * Initializes this converter with the DocBlock converter.
     *
     * @param DocBlockConverter $docBlockConverter
     */
    public function __construct(DocBlockConverter $docBlockConverter)
    {
        $this->docBlockConverter = $docBlockConverter;
    }

    /**
     * Export the given reflected property definition to the provided parent element.
     *
     * @param \DOMElement        $parent Element to augment.
     * @param PropertyDescriptor $property Element to export.
     *
     * @return \DOMElement
     */
    public function convert(\DOMElement $parent, PropertyDescriptor $property)
    {
        $fullyQualifiedNamespaceName = $property->getNamespace() instanceof NamespaceDescriptor
            ? $property->getNamespace()->getFullyQualifiedStructuralElementName()
            : $parent->getAttribute('namespace');

        $child = new \DOMElement('property');
        $parent->appendChild($child);

        $child->setAttribute('static', var_export($property->isStatic(), true));
        $child->setAttribute('visibility', $property->getVisibility());
        $child->setAttribute('namespace', $fullyQualifiedNamespaceName);
        $child->setAttribute('line', $property->getLine());

        $child->appendChild(new \DOMElement('name', '$' . $property->getName()));
        $child->appendChild(new \DOMElement('full_name', $property->getFullyQualifiedStructuralElementName()));
        $child->appendChild(new \DOMElement('default'))->appendChild(new \DOMText($property->getDefault()));

        $this->docBlockConverter->convert($child, $property);

        return $child;
    }
}
