<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer\Xml;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * Test class for \phpDocumentor\Transformer\Writer\Xml\ArgumentConverter.
 *
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Xml\ArgumentConverter
 * @covers ::<private>
 */
class ArgumentConverterTest extends TestCase
{
    /**
     * Tests whether the XML Element representing an argument is properly created.
     *
     * @covers ::convert
     */
    public function testArgumentXmlElementIsCreated()
    {
        // Arrange
        $tag = new ArgumentDescriptor();
        $tag->setName('name');
        $tag->setLine(100);
        $tag->setByReference(false);
        $tag->setDefault(null);
        $tag->setType(null);
        $parent = $this->prepareParentXMLElement();
        $argumentConverter = new ArgumentConverter();

        // Act
        $convertedElement = $argumentConverter->convert($parent, $tag);

        // Assert
        $this->assertSame('100', $convertedElement->getAttribute('line'));
        $this->assertSame('false', $convertedElement->getAttribute('by_reference'));
        $this->assertSame('name', $convertedElement->getElementsByTagName('name')->item(0)->nodeValue);
        $this->assertSame('', $convertedElement->getElementsByTagName('default')->item(0)->nodeValue);
        $this->assertSame('', $convertedElement->getElementsByTagName('type')->item(0)->nodeValue);
    }

    /**
     * Tests whether it is documented when an argument is by reference.
     *
     * @covers ::convert
     */
    public function testIfByReferenceIsDocumented()
    {
        // Arrange
        $argumentConverter = new ArgumentConverter();
        $parent = $this->prepareParentXMLElement();
        $tag = new ArgumentDescriptor();
        $tag->setName('name');
        $tag->setLine(100);
        $tag->setByReference(true);
        $tag->setDefault(null);
        $tag->setType(null);

        // Act
        $convertedElement = $argumentConverter->convert($parent, $tag);

        // Assert
        $this->assertSame('true', $convertedElement->getAttribute('by_reference'));
    }

    /**
     * Tests whether the type information for an argument is documented.
     *
     * @covers ::convert
     */
    public function testIfTypeInformationIsDocumented()
    {
        // Arrange
        $argumentConverter = new ArgumentConverter();
        $parent = $this->prepareParentXMLElement();
        $tag = new ArgumentDescriptor();
        $tag->setName('name');
        $tag->setLine(100);
        $tag->setByReference(true);
        $tag->setDefault(null);
        $tag->setType(new Compound([new String_(), new Integer()]));

        // Act
        $convertedElement = $argumentConverter->convert($parent, $tag);

        // Assert
        $this->assertSame('string|int', $convertedElement->getElementsByTagName('type')->item(0)->nodeValue);
    }

    /**
     * Tests whether the default for an argument is documented.
     *
     * @covers ::convert
     */
    public function testIfDefaultValueIsDocumented()
    {
        // Arrange
        $default = 'This is a default';
        $argumentConverter = new ArgumentConverter();
        $parent = $this->prepareParentXMLElement();
        $tag = new ArgumentDescriptor();
        $tag->setName('name');
        $tag->setLine(100);
        $tag->setByReference(true);
        $tag->setDefault($default);
        $tag->setType(new Compound([new String_(), new Integer()]));


        // Act
        $convertedElement = $argumentConverter->convert($parent, $tag);

        // Assert
        $this->assertSame($default, $convertedElement->getElementsByTagName('default')->item(0)->nodeValue);
    }

    /**
     * Creates an XML Element that can serve as parent.
     *
     * @return \DOMElement
     */
    protected function prepareParentXMLElement()
    {
        $document = new \DOMDocument();
        $parent = new \DOMElement('function');
        $document->appendChild($parent);

        return $parent;
    }
}
