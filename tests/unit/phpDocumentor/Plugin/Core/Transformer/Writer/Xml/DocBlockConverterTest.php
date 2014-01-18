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
use phpDocumentor\Descriptor\DescriptorAbstract;

/**
 * Test class for \phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter.
 *
 * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter
 */
class DocBlockConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests whether the XML Element representing a DocBlock is properly created.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::convert
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::addSummary
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::addDescription
     *
     * @return void
     */
    public function testArgumentXmlElementIsCreated()
    {
        // Arrange
        $parent           = $this->prepareParentXMLElement();
        $tagConverterMock = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Xml\TagConverter');
        $tagConverterMock->shouldIgnoreMissing();
        $docBlockConverter = new DocBlockConverter($tagConverterMock);
        $docBlock          = $this->createGenericDescriptorMock();
        $docBlock->shouldReceive('getTags')->andReturn(array());
        $docBlock->shouldReceive('getSummary')->andReturn('summary');
        $docBlock->shouldReceive('getDescription')->andReturn('description');

        // Act
        $convertedElement = $docBlockConverter->convert($parent, $docBlock);

        // Assert
        $this->assertSame('100', $convertedElement->getAttribute('line'));
        $this->assertSame('summary', $convertedElement->getElementsByTagName('description')->item(0)->nodeValue);
        $this->assertSame(0, $convertedElement->getElementsByTagName('tag')->length);
        $this->assertSame(
            'description',
            $convertedElement->getElementsByTagName('long-description')->item(0)->nodeValue
        );
    }

    /**
     * Tests whether the package is added onto the parent element.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::convert
     *
     * @return void
     */
    public function testParentPackageIsSetByDocBlocksPackage()
    {
        // Arrange
        $parent           = $this->prepareParentXMLElement();
        $tagConverterMock = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Xml\TagConverter');
        $tagConverterMock->shouldIgnoreMissing();
        $docBlockConverter = new DocBlockConverter($tagConverterMock);
        $docBlock          = $this->createGenericDescriptorMock();
        $docBlock->shouldReceive('getTags')->andReturn(array());

        // Act
        $docBlockConverter->convert($parent, $docBlock);

        // Assert
        $this->assertSame('This\Is\A\Package', $parent->getAttribute('package'));
    }

    /**
     * Tests whether tags are documented on the DocBlock.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::convert
     *
     * @return void
     */
    public function testIfTagsAreDocumented()
    {
        // Arrange
        $parent           = $this->prepareParentXMLElement();
        $tag              = m::mock('phpDocumentor\Descriptor\TagDescriptor');
        $tagConverterMock = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Xml\TagConverter');
        $tagConverterMock->shouldReceive('convert')->with(m::type('DOMElement'), $tag);
        $docBlockConverter = new DocBlockConverter($tagConverterMock);
        $docBlock          = $this->createGenericDescriptorMock();
        $docBlock->shouldReceive('getTags')->andReturn(array(array($tag)));

        // Act
        $docBlockConverter->convert($parent, $docBlock);

        // Assert, no assertions needed as this is covered in the mocks above
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
     * @return m\MockInterface|DescriptorAbstract
     */
    protected function createGenericDescriptorMock()
    {
        $tag = m::mock('phpDocumentor\\Descriptor\\DescriptorAbstract');
        $tag->shouldReceive('getLine')->andReturn(100);
        $tag->shouldReceive('getPackage')->andReturn('This\Is\A\Package');
        $tag->shouldIgnoreMissing();

        return $tag;
    }
}
