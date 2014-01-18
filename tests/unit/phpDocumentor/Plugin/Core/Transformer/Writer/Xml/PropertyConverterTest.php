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
use phpDocumentor\Descriptor\PropertyDescriptor;

/**
 * Test class for \phpDocumentor\Plugin\Core\Transformer\Writer\Xml\PropertyConverter.
 *
 * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\PropertyConverter
 */
class PropertyConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests whether the XML Element representing a property is properly created.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\PropertyConverter::convert
     *
     * @return void
     */
    public function testPropertyXmlElementIsCreated()
    {
        // Arrange
        $property          = $this->createPropertyDescriptorMock();
        $propertyConverter = $this->createFixture($property);
        $parent            = $this->prepareParentXMLElement();
        $parent->setAttribute('namespace', 'phpDocumentor');

        // Act
        $convertedElement = $propertyConverter->convert($parent, $property);

        // Assert
        $this->assertSame('false', $convertedElement->getAttribute('static'));
        $this->assertSame('protected', $convertedElement->getAttribute('visibility'));
        $this->assertSame('100', $convertedElement->getAttribute('line'));
        $this->assertSame('phpDocumentor', $convertedElement->getAttribute('namespace'));
        $this->assertSame('$Property', $convertedElement->getElementsByTagName('name')->item(0)->nodeValue);
        $this->assertSame(
            'phpDocumentor\Class::$Property',
            $convertedElement->getElementsByTagName('full_name')->item(0)->nodeValue
        );
        $this->assertSame('defaultString', $convertedElement->getElementsByTagName('default')->item(0)->nodeValue);
    }

    /**
     * Tests whether the XML Element representing a property is properly created.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\PropertyConverter::convert
     *
     * @return void
     */
    public function testNamespaceNameIsTakenFromNamespaceDescriptorIfPresent()
    {
        // Arrange
        $parent              = $this->prepareParentXMLElement();
        $namespaceDescriptor = m::mock('phpDocumentor\Descriptor\NamespaceDescriptor');
        $namespaceDescriptor->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('MySpace');
        $property              = $this->createPropertyDescriptorMock();
        $property->shouldReceive('getNamespace')->andReturn($namespaceDescriptor);
        $propertyConverter = $this->createFixture($property);

        // Act
        $convertedElement = $propertyConverter->convert($parent, $property);

        // Assert
        $this->assertSame('MySpace', $convertedElement->getAttribute('namespace'));
    }

    /**
     * Creates an XML Element that can serve as parent.
     *
     * @return \DOMElement
     */
    protected function prepareParentXMLElement()
    {
        $document = new \DOMDocument();
        $parent   = new \DOMElement('class');
        $document->appendChild($parent);

        return $parent;
    }

    /**
     * Creates a mock for the PropertyDescriptor class.
     *
     * @return m\MockInterface|DescriptorAbstract
     */
    protected function createPropertyDescriptorMock()
    {
        $property = m::mock('phpDocumentor\\Descriptor\\PropertyDescriptor');
        $property->shouldReceive('getLine')->andReturn(100);
        $property->shouldReceive('isStatic')->andReturn(false);
        $property->shouldReceive('getVisibility')->andReturn('protected');
        $property->shouldReceive('getName')->andReturn('Property');
        $property->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('phpDocumentor\Class::$Property');
        $property->shouldReceive('getDefault')->andReturn('defaultString');
        $property->shouldIgnoreMissing();

        return $property;
    }

    /**
     * Creates the PropertyConverter fixture with a DocBlock mock.
     *
     * @param PropertyDescriptor $property
     *
     * @return PropertyConverter
     */
    protected function createFixture(PropertyDescriptor $property)
    {
        $docBlockConverter = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter');
        $docBlockConverter->shouldReceive('convert')->with(m::type('DOMElement'), $property);

        return new PropertyConverter($docBlockConverter);
    }
}
