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

use phpDocumentor\Descriptor\InterfaceDescriptor;

/**
 * Converter used to create an XML Element representing the interface and its Constants, Methods and DocBlock.
 *
 * In order to convert the DocBlock to its XML representation this class requires the respective converter.
 */
class InterfaceConverter
{
    /** @var DocBlockConverter object used to convert DocBlocks into their XML counterpart */
    protected $docBlockConverter;

    /** @var MethodConverter object used to convert methods into their XML counterpart */
    protected $methodConverter;

    /** @var ConstantConverter object used to convert constants into their XML counterpart */
    protected $constantConverter;

    /**
     * Initializes this converter with the DocBlock converter.
     *
     * @param DocBlockConverter $docBlockConverter
     * @param MethodConverter   $methodConverter
     * @param ConstantConverter $constantConverter
     */
    public function __construct(
        DocBlockConverter $docBlockConverter,
        MethodConverter $methodConverter,
        ConstantConverter $constantConverter
    ) {
        $this->docBlockConverter = $docBlockConverter;
        $this->methodConverter   = $methodConverter;
        $this->constantConverter = $constantConverter;
    }

    /**
     * Export the given reflected interface definition to the provided parent element.
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
     * @param InterfaceDescriptor $interface Element to export.
     *
     * @return \DOMElement
     */
    public function convert(\DOMElement $parent, InterfaceDescriptor $interface)
    {
        $child = new \DOMElement('interface');
        $parent->appendChild($child);

        /** @var InterfaceDescriptor $parentInterface */
        foreach ($interface->getParent() as $parentInterface) {
            $parentFqcn = is_string($parentInterface) === false
                ? $parentInterface->getFullyQualifiedStructuralElementName()
                : $parentInterface;
            $child->appendChild(new \DOMElement('extends', $parentFqcn));
        }

        $namespace = $interface->getNamespace()->getFullyQualifiedStructuralElementName();
        $child->setAttribute('namespace', ltrim($namespace, '\\'));
        $child->setAttribute('line', $interface->getLine());

        $child->appendChild(new \DOMElement('name', $interface->getName()));
        $child->appendChild(new \DOMElement('full_name', $interface->getFullyQualifiedStructuralElementName()));

        $this->docBlockConverter->convert($child, $interface);

        foreach ($interface->getConstants() as $constant) {
            $this->constantConverter->convert($child, $constant);
        }

        foreach ($interface->getMethods() as $method) {
            $this->methodConverter->convert($child, $method);
        }

        return $child;
    }
}
