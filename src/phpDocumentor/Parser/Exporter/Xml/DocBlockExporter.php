<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Parser\Exporter\Xml
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Exporter\Xml;

/**
 * Exports the details of an elements' DocBlock to XML.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
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
     * @param \DOMElement                            $parent   The parent element
     *     to augment.
     * @param \phpDocumentor\Reflection\BaseReflector $element The data source.
     *
     * @return void
     */
    public function export(
        \DOMElement $parent, $element
    ) {
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

    protected function addDescription(
        \DOMElement $child, \phpDocumentor\Reflection\DocBlock $docblock
    ) {
        $node = $child->ownerDocument->createCDATASection(
            $docblock->getShortDescription()
        );
        $description = new \DOMElement('description');
        $child->appendChild($description);
        $description->appendChild($node);
    }

    /**
     * Adds the DocBlock's long description to the $child element,
     *
     * This method also removes all binary characters to prevent issues in the
     * XML.
     *
     * @param \DOMElement $child
     * @param \phpDocumentor\Reflection\DocBlock $docblock
     *
     * @return void
     */
    protected function addLongDescription(
        \DOMElement $child, \phpDocumentor\Reflection\DocBlock $docblock
    ) {
        $node = $child->ownerDocument->createCDATASection(
            $docblock->getLongDescription()->getFormattedContents()
        );

        $element = new \DOMElement('long-description');
        $child->appendChild($element);
        $element->appendChild($node);
    }

    /**
     * Strips the binary characters of the given string.
     *
     * @param string $contents The contents to strip
     *
     * @return string
     */
    protected function stripBinaryCharacters($contents)
    {
        return preg_replace("/[^\x9\xA\xD\x20-\x7F]/", '', $contents);
    }

    protected function addTags(
        \DOMElement $child, $tags, $element
    ) {
        foreach ($tags as $tag) {
            $object = new DocBlockTag();
            $object->export($child, $tag, $element);
        }
    }

    protected function setParentsPackage(
        \DOMElement $parent, \phpDocumentor\Reflection\DocBlock $docblock,
        $element
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