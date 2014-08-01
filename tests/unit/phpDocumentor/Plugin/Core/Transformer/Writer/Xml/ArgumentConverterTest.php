<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer\Xml;

use Mockery as m;
use phpDocumentor\Descriptor\ArgumentDescriptor;

/**
 * Test class for \phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ArgumentConverter.
 *
 * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ArgumentConverter
 */
class ArgumentConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests whether the XML Element representing an argument is properly created.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ArgumentConverter::convert
     *
     * @return void
     */
    public function testArgumentXmlElementIsCreated()
    {
        // Arrange
        $tag = $this->createArgumentDescriptorMock();
        $tag->shouldReceive('isByReference')->andReturn(false);
        $tag->shouldReceive('getDefault')->andReturn(null);
        $tag->shouldReceive('getTypes')->andReturn(array());
        $parent            = $this->prepareParentXMLElement();
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
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ArgumentConverter::convert
     *
     * @return void
     */
    public function testIfByReferenceIsDocumented()
    {
        // Arrange
        $argumentConverter = new ArgumentConverter();
        $parent            = $this->prepareParentXMLElement();
        $tag               = $this->createArgumentDescriptorMock();
        $tag->shouldReceive('isByReference')->andReturn(true);
        $tag->shouldReceive('getTypes')->andReturn(array());

        // Act
        $convertedElement = $argumentConverter->convert($parent, $tag);

        // Assert
        $this->assertSame('true', $convertedElement->getAttribute('by_reference'));
    }

    /**
     * Tests whether the type information for an argument is documented.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ArgumentConverter::convert
     *
     * @return void
     */
    public function testIfTypeInformationIsDocumented()
    {
        // Arrange
        $argumentConverter = new ArgumentConverter();
        $parent            = $this->prepareParentXMLElement();
        $tag               = $this->createArgumentDescriptorMock();
        $tag->shouldReceive('getTypes')->andReturn(array('string', 'integer'));

        // Act
        $convertedElement = $argumentConverter->convert($parent, $tag);

        // Assert
        $this->assertSame('string|integer', $convertedElement->getElementsByTagName('type')->item(0)->nodeValue);
    }

    /**
     * Tests whether the default for an argument is documented.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ArgumentConverter::convert
     *
     * @return void
     */
    public function testIfDefaultValueIsDocumented()
    {
        // Arrange
        $default           = 'This is a default';
        $argumentConverter = new ArgumentConverter();
        $parent            = $this->prepareParentXMLElement();
        $tag               = $this->createArgumentDescriptorMock();
        $tag->shouldReceive('getDefault')->andReturn($default);
        $tag->shouldReceive('getTypes')->andReturn(array());

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
        $parent   = new \DOMElement('function');
        $document->appendChild($parent);

        return $parent;
    }

    /**
     * Creates a mock for the ArgumentDescriptor class.
     *
     * @return m\MockInterface|ArgumentDescriptor
     */
    protected function createArgumentDescriptorMock()
    {
        $tag = m::mock('phpDocumentor\\Descriptor\\ArgumentDescriptor');
        $tag->shouldReceive('getLine')->andReturn(100);
        $tag->shouldReceive('getName')->andReturn('name');
        $tag->shouldIgnoreMissing();

        return $tag;
    }
}
