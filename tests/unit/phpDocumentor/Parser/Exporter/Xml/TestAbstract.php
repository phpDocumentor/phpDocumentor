<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Exporter\Xml;

/**
 * Abstract class for the XML Exporter unit tests.
 */
class TestAbstract extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a DOMElement to serve as child element.
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement
     */
    protected function createChildXmlNode($doc)
    {
        $child = new \DOMElement('child');
        $doc->appendChild($child);
        return $child;
    }

    /**
     * Creates a DOMElement to serve as parent container.
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement
     */
    protected function createParentXmlNode($doc)
    {
        $parent = new \DOMElement('parent');
        $doc->appendChild($parent);
        return $parent;
    }

    /**
     * Returns a fixture specific for XML Exporters.
     *
     * The type indicates the last part of the class, properly capitalized.
     *
     * @param string $type
     *
     * @return \phpDocumentor\Parser\Exporter\ExporterAbstract
     */
    protected function createFixture($type)
    {
        $class = '\phpDocumentor\Parser\Exporter\Xml\\' . $type.'Exporter';
        return new $class($this->getMock('\phpDocumentor\Parser'));
    }
}
