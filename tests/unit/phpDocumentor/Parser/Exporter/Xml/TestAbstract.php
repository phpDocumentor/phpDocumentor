<?php
class phpDocumentor_Parser_Exporter_Xml_TestAbstract
    extends PHPUnit_Framework_TestCase
{
    /**
     * Creates a DOMElement to serve as child element.
     *
     * @param DOMDocument $doc
     *
     * @return DOMElement
     */
    protected function createChildXmlNode($doc)
    {
        $child = new DOMElement('child');
        $doc->appendChild($child);
        return $child;
    }

    /**
     * Creates a DOMElement to serve as parent container.
     *
     * @param DOMDocument $doc
     *
     * @return DOMElement
     */
    protected function createParentXmlNode($doc)
    {
        $parent = new DOMElement('parent');
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
     * @return phpDocumentor_Parser_Exporter_Abstract
     */
    protected function createFixture($type)
    {
        $class = 'phpDocumentor_Parser_Exporter_Xml_' . $type;
        return new $class($this->getMock('phpDocumentor_Parser'));
    }
}
