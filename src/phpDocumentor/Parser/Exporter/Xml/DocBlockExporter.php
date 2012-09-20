<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Exporter\Xml;

use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\DocBlock;

/**
 * Exports the details of an elements' DocBlock to XML.
 */
class DocBlockExporter
{
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
     * @param \DOMElement   $parent  The parent element to augment.
     * @param BaseReflector $element The data source.
     *
     * @return void
     */
    public function export(\DOMElement $parent, $element)
    {
        $docblock = $element->getDocBlock();
        if (!$docblock) {
            $parent->setAttribute('package', $element->getDefaultPackageName());
            return;
        }

        $child = new \DOMElement('docblock');
        $parent->appendChild($child);

        // TODO: custom attached member variable, make real
        $child->setAttribute('line', $docblock->line_number);

        $this->addDescription($child, $docblock);
        $this->addLongDescription($child, $docblock);
        $this->addTags($child, $docblock->getTags(), $element);
        $this->setParentsPackage($parent, $docblock, $element);
    }

    /**
     * Adds the short description of $docblock to the given node as description
     * field.
     *
     * @param \DOMElement $node
     * @param DocBlock $docblock
     *
     * @return void
     */
    protected function addDescription(\DOMElement $node, DocBlock $docblock)
    {
        $cdata = $node->ownerDocument->createCDATASection(
            $docblock->getShortDescription()
        );
        $description = new \DOMElement('description');
        $node->appendChild($description);
        $description->appendChild($cdata);
    }

    /**
     * Adds the DocBlock's long description to the $child element,
     *
     * @param \DOMElement $child
     * @param DocBlock $docblock
     *
     * @return void
     */
    protected function addLongDescription(
        \DOMElement $child, \phpDocumentor\Reflection\DocBlock $docblock
    ) {
        $contents = $docblock->getLongDescription()->getFormattedContents();
        $node = $child->ownerDocument->createCDATASection($contents);

        $element = new \DOMElement('long-description');
        $child->appendChild($element);
        $element->appendChild($node);
    }

    /**
     * Adds each tag to the $xml_node.
     *
     * @param \DOMElement    $xml_node
     * @param DocBlock\Tag[] $tags
     * @param BaseReflector  $element
     *
     * @return void
     */
    protected function addTags(\DOMElement $xml_node, $tags, $element)
    {
        foreach ($tags as $tag) {
            $object = new DocBlockTagExporter();
            $object->export($xml_node, $tag, $element);
        }
    }

    /**
     * Sets the package of the parent element.
     *
     * This method inspects the current DocBlock and extract an @package
     * element. If that tag is present than the associated element's package
     * name is set to that value.
     *
     * If no @package tag is present in the DocBlock then the default package
     * name is set.
     *
     * @param \DOMElement   $parent
     * @param DocBlock      $docblock
     * @param BaseReflector $element
     *
     * @return void
     */
    protected function setParentsPackage(
        \DOMElement $parent, DocBlock $docblock, $element
    ) {
        /** @var \phpDocumentor\Reflection\DocBlock\Tag $package */
        $package = current($docblock->getTagsByName('package'));

        /** @var \phpDocumentor\Reflection\DocBlock\Tag $subpackage */
        $subpackage = current($docblock->getTagsByName('subpackage'));

        $package_name = '';
        if ($package) {
            $package_name = str_replace(
                array('.', '_'),
                '\\',
                $package->getContent()
                . ($subpackage ? '\\' . $subpackage->getContent() : '')
            );
        }

        if (!$package_name) {
            $package_name = $element->getDefaultPackageName();
        }

        $parent->setAttribute('package', $package_name);
    }

}