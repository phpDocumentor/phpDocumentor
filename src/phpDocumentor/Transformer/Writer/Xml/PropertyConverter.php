<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer\Xml;

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
        $child->setAttribute('line', (string) $property->getLine());

        $child->appendChild(new \DOMElement('name', '$' . $property->getName()));
        $child->appendChild(new \DOMElement('full_name', (string) $property->getFullyQualifiedStructuralElementName()));
        $child->appendChild(new \DOMElement('default'))->appendChild(new \DOMText((string) $property->getDefault()));

        $this->docBlockConverter->convert($child, $property);

        return $child;
    }
}
