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
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;

/**
 * Test class for \phpDocumentor\Plugin\Core\Transformer\Writer\Xml\TraitConverter.
 *
 * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\TraitConverter
 */
class TraitConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests whether the XML Element representing a trait is properly created.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\TraitConverter::convert
     *
     * @return void
     */
    public function testTraitXmlElementIsCreated()
    {
        // Arrange
        $methodDescriptor    = m::mock('phpDocumentor\Descriptor\MethodDescriptor');
        $propertyDescriptor  = m::mock('phpDocumentor\Descriptor\PropertyDescriptor');
        $namespaceDescriptor = m::mock('phpDocumentor\Descriptor\NamespaceDescriptor');
        $namespaceDescriptor->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('phpDocumentor');
        $trait = $this->createTraitDescriptorMock();
        $trait->shouldReceive('getMethods')->andReturn(array($methodDescriptor));
        $trait->shouldReceive('getProperties')->andReturn(array($propertyDescriptor));
        $trait->shouldReceive('getNamespace')->andReturn($namespaceDescriptor);
        $parent = $this->prepareParentXMLElement();
        $traitConverter = $this->createFixture($trait, $methodDescriptor, $propertyDescriptor);

        // Act
        $convertedElement = $traitConverter->convert($parent, $trait);

        // Assert
        $this->assertSame('100', $convertedElement->getAttribute('line'));
        $this->assertSame('phpDocumentor', $convertedElement->getAttribute('namespace'));
        $this->assertSame('Trait', $convertedElement->getElementsByTagName('name')->item(0)->nodeValue);
        $this->assertSame(
            'phpDocumentor\Trait',
            $convertedElement->getElementsByTagName('full_name')->item(0)->nodeValue
        );
    }

    /**
     * Creates an XML Element that can serve as parent.
     *
     * @return \DOMElement
     */
    protected function prepareParentXMLElement()
    {
        $document = new \DOMDocument();
        $parent   = new \DOMElement('file');
        $document->appendChild($parent);

        return $parent;
    }

    /**
     * Creates a mock for the TraitDescriptor class.
     *
     * @return m\MockTrait|DescriptorAbstract
     */
    protected function createTraitDescriptorMock()
    {
        $trait = m::mock('phpDocumentor\\Descriptor\\TraitDescriptor');
        $trait->shouldReceive('getLine')->andReturn(100);
        $trait->shouldReceive('getName')->andReturn('Trait');
        $trait->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('phpDocumentor\Trait');
        $trait->shouldIgnoreMissing();

        return $trait;
    }

    /**
     * Creates the TraitConverter fixture with a DocBlock mock.
     *
     * @param TraitDescriptor    $trait
     * @param MethodDescriptor   $method
     * @param PropertyDescriptor $property
     *
     * @return TraitConverter
     */
    protected function createFixture(TraitDescriptor $trait, $method, $property)
    {
        $docBlockConverter = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter');
        $docBlockConverter->shouldReceive('convert')->with(m::type('DOMElement'), $trait);

        $methodConverter = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Xml\MethodConverter');
        $methodConverter->shouldReceive('convert')->with(m::type('DOMElement'), $method);

        $propertyConverter = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Xml\PropertyConverter');
        $propertyConverter->shouldReceive('convert')->with(m::type('DOMElement'), $property);

        return new TraitConverter($docBlockConverter, $methodConverter, $propertyConverter);
    }
}
