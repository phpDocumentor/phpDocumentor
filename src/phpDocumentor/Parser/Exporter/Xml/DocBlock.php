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

/**
 * Exports the details of an elements' DocBlock to XML.
 *
 * @category phpDocumentor
 * @package  Parser\Exporter\Xml
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Parser_Exporter_Xml_DocBlock
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
     * @param DOMElement                            $parent   The parent element
     *     to augment.
     * @param phpDocumentor_Reflection_DocBlockedAbstract $element The data source.
     *
     * @return void
     */
    public function export(
        DOMElement $parent, phpDocumentor_Reflection_DocBlockedAbstract $element
    ) {
        $docblock = $element->getDocBlock();
        if (!$docblock) {
            $parent->setAttribute('package', $element->getDefaultPackageName());
            return;
        }

        $child = new DOMElement('docblock');
        $parent->appendChild($child);

        // TODO: custom attached member variable, make real
        $child->setAttribute('line', $docblock->line_number);

        $this->addDescription($child, $docblock);
        $this->addLongDescription($child, $docblock);
        $this->addTags($child, $docblock->getTags(), $element);
        $this->setParentsPackage($parent, $docblock, $element);
    }

    protected function addDescription(
        DOMElement $child, phpDocumentor_Reflection_DocBlock $docblock
    ) {
        $node = $child->ownerDocument->createCDATASection(
            $docblock->getShortDescription()
        );
        $description = new DOMElement('description');
        $child->appendChild($description);
        $description->appendChild($node);
    }

    protected function addLongDescription(
        DOMElement $child, phpDocumentor_Reflection_DocBlock $docblock
    ) {
        $node = $child->ownerDocument->createCDATASection(
            $docblock->getLongDescription()->getFormattedContents()
        );

        $element = new DOMElement('long-description');
        $child->appendChild($element);
        $element->appendChild($node);
    }

    protected function addTags(
        DOMElement $child, $tags, phpDocumentor_Reflection_DocBlockedAbstract $element
    ) {
        foreach ($tags as $tag) {
            $object = new phpDocumentor_Parser_Exporter_Xml_DocBlockTag();
            $object->export($child, $tag, $element);
        }
    }

    protected function setParentsPackage(
        DOMElement $parent, phpDocumentor_Reflection_DocBlock $docblock,
        phpDocumentor_Reflection_DocBlockedAbstract $element
    ) {
        /** @var phpDocumentor_Reflection_DocBlock_Tag $package */
        $package = current($docblock->getTagsByName('package'));

        /** @var phpDocumentor_Reflection_DocBlock_Tag $subpackage */
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