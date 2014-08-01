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

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Transformer\Router\RouterAbstract;

/**
 * Converter used to create an XML Element representing a DocBlock and its tags.
 *
 * In order to convert the tags to their XML representation this class requires the respective converter.
 */
class DocBlockConverter
{
    /** @var TagConverter Converter used to generate XML elements from TagDescriptors */
    protected $tagConverter;

    /** @var RouterAbstract */
    private $router;

    /**
     * Stores the converter for tags on this converter.
     *
     * @param TagConverter   $tagConverter
     * @param RouterAbstract $router
     */
    public function __construct(TagConverter $tagConverter, RouterAbstract $router)
    {
        $this->tagConverter = $tagConverter;
        $this->router       = $router;
    }

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
     * @param \DOMElement        $parent  The parent element to augment.
     * @param DescriptorAbstract $element The data source.
     *
     * @return \DOMElement
     */
    public function convert(\DOMElement $parent, DescriptorAbstract $element)
    {
        $child = new \DOMElement('docblock');
        $parent->appendChild($child);

        $child->setAttribute('line', $element->getLine());
        $package = str_replace('&', '&amp;', ltrim($element->getPackage(), '\\'));
        $parent->setAttribute('package', $package ?: 'global');

        $this->addSummary($child, $element);
        $this->addDescription($child, $element);
        $this->addTags($child, $element);
        $this->addInheritedFromTag($child, $element);

        return $child;
    }

    /**
     * Adds the short description of $docblock to the given node as description
     * field.
     *
     * @param \DOMElement        $node
     * @param DescriptorAbstract $element
     *
     * @return void
     */
    protected function addSummary(\DOMElement $node, DescriptorAbstract $element)
    {
        $node->appendChild(new \DOMElement('description'))
             ->appendChild(new \DOMText($element->getSummary()));
    }

    /**
     * Adds the DocBlock's long description to the $child element,
     *
     * @param \DOMElement        $node
     * @param DescriptorAbstract $element
     *
     * @return void
     */
    protected function addDescription(\DOMElement $node, DescriptorAbstract $element)
    {
        $node->appendChild(new \DOMElement('long-description'))
            ->appendChild(new \DOMText($element->getDescription()));
    }

    /**
     * Adds each tag to the XML Node representing the DocBlock.
     *
     * The Descriptor contains an array of tag groups (that are tags grouped by their name), which in itself contains
     * an array of the individual tags.
     *
     * @param \DOMElement        $docBlock
     * @param DescriptorAbstract $descriptor
     *
     * @return void
     */
    protected function addTags(\DOMElement $docBlock, $descriptor)
    {
        foreach ($descriptor->getTags() as $tagGroup) {
            if (! $tagGroup) {
                continue;
            }

            foreach ($tagGroup as $tag) {
                $this->tagConverter->convert($docBlock, $tag);
            }
        }
    }

    /**
     * Adds the 'inherited_from' tag when a Descriptor inherits from another Descriptor.
     *
     * @param \DOMElement        $docBlock
     * @param DescriptorAbstract $descriptor
     *
     * @return void
     */
    protected function addInheritedFromTag(\DOMElement $docBlock, $descriptor)
    {
        $parentElement = $descriptor->getInheritedElement();
        if (! $parentElement instanceof $descriptor) {
            return;
        }

        $child = new \DOMElement('tag');
        $docBlock->appendChild($child);

        $rule = $this->router->match($parentElement);

        $child->setAttribute('name', 'inherited_from');
        $child->setAttribute('description', $parentElement->getFullyQualifiedStructuralElementName());
        $child->setAttribute('refers', $parentElement->getFullyQualifiedStructuralElementName());
        $child->setAttribute('link', $rule ? $rule->generate($parentElement) : '');
    }
}
