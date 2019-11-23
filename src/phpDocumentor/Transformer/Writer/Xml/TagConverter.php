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

use phpDocumentor\Descriptor\Tag\BaseTypes\TypedAbstract;
use phpDocumentor\Descriptor\Tag\BaseTypes\TypedVariableAbstract;
use phpDocumentor\Descriptor\Tag\DeprecatedDescriptor;
use phpDocumentor\Descriptor\Tag\LinkDescriptor;
use phpDocumentor\Descriptor\Tag\MethodDescriptor;
use phpDocumentor\Descriptor\Tag\SeeDescriptor;
use phpDocumentor\Descriptor\Tag\SinceDescriptor;
use phpDocumentor\Descriptor\Tag\UsesDescriptor;
use phpDocumentor\Descriptor\Tag\VersionDescriptor;
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

        if ($tag instanceof TypedVariableAbstract) {
            $child->setAttribute('variable', str_replace('&', '&amp;', $tag->getVariableName()));
        }

        if ($tag instanceof SeeDescriptor || $tag instanceof UsesDescriptor) {
            $child->setAttribute('link', str_replace('&', '&amp;', $tag->getReference()));
        }

        if ($tag instanceof LinkDescriptor) {
            $child->setAttribute('link', str_replace('&', '&amp;', $tag->getLink()));
        }

        if ($tag instanceof MethodDescriptor) {
            $child->setAttribute('method_name', str_replace('&', '&amp;', $tag->getMethodName()));
        }

        return $child;
    }

    /**
     * Returns the description from the Tag with the version prepended when applicable.
     *
     * @todo the version should not be prepended here but in templates; remove this.
     * @return string
     */
    protected function getDescription(TagDescriptor $tag)
    {
        $description = '';

        if ($tag instanceof VersionDescriptor ||
            $tag instanceof DeprecatedDescriptor ||
            $tag instanceof SinceDescriptor
        ) {
            $description .= $tag->getVersion() . ' ';
        }

        $description .= $tag->getDescription();

        return trim($description);
    }

    /**
     * Adds type elements and a type attribute to the tag if a method 'getTypes' is present.
     */
    protected function addTypes(TagDescriptor $tag, \DOMElement $child)
    {
        $typeString = '';

        if ($tag instanceof TypedAbstract) {
            $types = $tag->getType();

            if ($types instanceof \IteratorAggregate) {
                foreach ($types as $type) {
                    $typeString .= $type . '|';

                    /** @var \DOMElement $typeNode */
                    $typeNode = $child->appendChild(new \DOMElement('type'));
                    $typeNode->appendChild(new \DOMText((string) $type));
                }
            } else {
                $typeString .= $types . '|';

                /** @var \DOMElement $typeNode */
                $typeNode = $child->appendChild(new \DOMElement('type'));
                $typeNode->appendChild(new \DOMText((string) $types));
            }

            $child->setAttribute('type', str_replace('&', '&amp;', rtrim($typeString, '|')));
        }
    }
}
