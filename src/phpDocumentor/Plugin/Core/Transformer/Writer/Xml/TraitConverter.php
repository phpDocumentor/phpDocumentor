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

use phpDocumentor\Descriptor\TraitDescriptor;

/**
 * Converter used to create an XML Element representing the Trait and its Methods, Properties and DocBlock.
 *
 * In order to convert the DocBlock to its XML representation this class requires the respective converter.
 */
class TraitConverter
{
    /** @var DocBlockConverter object used to convert DocBlocks into their XML counterpart */
    protected $docBlockConverter;

    /** @var MethodConverter object used to convert methods into their XML counterpart */
    protected $methodConverter;

    /** @var PropertyConverter object used to convert properties into their XML counterpart */
    protected $propertyConverter;

    /**
     * Initializes this converter with the DocBlock converter.
     *
     * @param DocBlockConverter $docBlockConverter
     * @param MethodConverter   $methodConverter
     * @param PropertyConverter $propertyConverter
     */
    public function __construct(
        DocBlockConverter $docBlockConverter,
        MethodConverter $methodConverter,
        PropertyConverter $propertyConverter
    ) {
        $this->docBlockConverter = $docBlockConverter;
        $this->methodConverter   = $methodConverter;
        $this->propertyConverter = $propertyConverter;
    }

    /**
     * Export the given reflected Trait definition to the provided parent element.
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
     * @param \DOMElement        $parent Element to augment.
     * @param TraitDescriptor $trait Element to export.
     *
     * @return \DOMElement
     */
    public function convert(\DOMElement $parent, TraitDescriptor $trait)
    {
        $child = new \DOMElement('trait');
        $parent->appendChild($child);

        $namespace = $trait->getNamespace()->getFullyQualifiedStructuralElementName();
        $child->setAttribute('namespace', ltrim($namespace, '\\'));
        $child->setAttribute('line', $trait->getLine());

        $child->appendChild(new \DOMElement('name', $trait->getName()));
        $child->appendChild(new \DOMElement('full_name', $trait->getFullyQualifiedStructuralElementName()));

        $this->docBlockConverter->convert($child, $trait);

        foreach ($trait->getProperties() as $property) {
            $this->propertyConverter->convert($child, $property);
        }

        foreach ($trait->getMethods() as $method) {
            $this->methodConverter->convert($child, $method);
        }

        return $child;
    }
}
