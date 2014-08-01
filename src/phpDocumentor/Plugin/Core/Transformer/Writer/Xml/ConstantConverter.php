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

use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;

/**
 * Converter used to create an XML Element representing the constant and its DocBlock.
 *
 * In order to convert the DocBlock to its XML representation this class requires the respective converter.
 */
class ConstantConverter
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
     * Export the given reflected constant definition to the provided parent element.
     *
     * @param \DOMElement        $parent Element to augment.
     * @param ConstantDescriptor $constant Element to export.
     *
     * @return \DOMElement
     */
    public function convert(\DOMElement $parent, ConstantDescriptor $constant)
    {
        $fullyQualifiedNamespaceName = $constant->getNamespace() instanceof NamespaceDescriptor
            ? $constant->getNamespace()->getFullyQualifiedStructuralElementName()
            : $parent->getAttribute('namespace');

        $child = new \DOMElement('constant');
        $parent->appendChild($child);

        $child->setAttribute('namespace', ltrim($fullyQualifiedNamespaceName, '\\'));
        $child->setAttribute('line', $constant->getLine());

        $child->appendChild(new \DOMElement('name', $constant->getName()));
        $child->appendChild(new \DOMElement('full_name', $constant->getFullyQualifiedStructuralElementName()));
        $child->appendChild(new \DOMElement('value'))->appendChild(new \DOMText($constant->getValue()));

        $this->docBlockConverter->convert($child, $constant);

        return $child;
    }
}
