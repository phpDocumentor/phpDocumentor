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

use phpDocumentor\Descriptor\TagDescriptor;

/**
 * Creates an XML Element 'tag' and appends it to the provided parent element.
 *
 * With this class we convert a TagDescriptor, or any child thereof, into an XML element that is subsequently appended
 * onto a provided parent element (usually an XML Element that represents a DocBlock).
 *
 * During the conversion process the generated XML Element is enriched with additional elements and attributes based on
 * which tags are provided (or more specifically which methods that support).
 */
class TagConverter
{
    /**
     * Export this tag to the given DocBlock.
     *
     * @param \DOMElement   $parent  Element to augment.
     * @param TagDescriptor $tag     The tag to export.
     *
     * @return \DOMElement
     */
    public function convert(\DOMElement $parent, TagDescriptor $tag)
    {
        $description = $this->getDescription($tag);

        $child = new \DOMElement('tag');
        $parent->appendChild($child);

        $child->setAttribute('name', str_replace('&', '&amp;', $tag->getName()));
        $child->setAttribute('line', $parent->getAttribute('line'));
        $child->setAttribute('description', str_replace('&', '&amp;', $description));
        $this->addTypes($tag, $child);

        // TODO: make the tests below configurable from the outside so that more could be added using plugins
        if (method_exists($tag, 'getVariableName')) {
            $child->setAttribute('variable', str_replace('&', '&amp;', $tag->getVariableName()));
        }
        if (method_exists($tag, 'getReference')) {
            $child->setAttribute('link', str_replace('&', '&amp;', $tag->getReference()));
        }
        if (method_exists($tag, 'getLink')) {
            $child->setAttribute('link', str_replace('&', '&amp;', $tag->getLink()));
        }
        if (method_exists($tag, 'getMethodName')) {
            $child->setAttribute('method_name', str_replace('&', '&amp;', $tag->getMethodName()));
        }

        return $child;
    }

    /**
     * Returns the description from the Tag with the version prepended when applicable.
     *
     * @param TagDescriptor $tag
     *
     * @todo the version should not be prepended here but in templates; remove this.
     *
     * @return string
     */
    protected function getDescription(TagDescriptor $tag)
    {
        $description = '';

        //@version, @deprecated, @since
        if (method_exists($tag, 'getVersion')) {
            $description .= $tag->getVersion() . ' ';
        }

        $description .= $tag->getDescription();

        return trim($description);
    }

    /**
     * Adds type elements and a type attribute to the tag if a method 'getTypes' is present.
     *
     * @param TagDescriptor $tag
     * @param \DOMElement   $child
     *
     * @return void
     */
    protected function addTypes(TagDescriptor $tag, \DOMElement $child)
    {
        if (!method_exists($tag, 'getTypes')) {
            return;
        }

        $typeString = '';
        foreach ($tag->getTypes() as $type) {
            $typeString .= $type . '|';

            /** @var \DOMElement $typeNode */
            $typeNode = $child->appendChild(new \DOMElement('type'));
            $typeNode->appendChild(new \DOMText($type));
        }

        $child->setAttribute('type', str_replace('&', '&amp;', rtrim($typeString, '|')));
    }
}
