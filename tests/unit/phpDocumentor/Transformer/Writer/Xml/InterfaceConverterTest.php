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

use Mockery as m;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;

/**
 * Test class for \phpDocumentor\Transformer\Writer\Xml\InterfaceConverter.
 *
 * @covers \phpDocumentor\Transformer\Writer\Xml\InterfaceConverter
 */
class InterfaceConverterTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * Tests whether the XML Element representing a interface is properly created.
     *
     * @covers \phpDocumentor\Transformer\Writer\Xml\InterfaceConverter::convert
     */
    public function testInterfaceXmlElementIsCreated() : void
    {
        // Arrange
        $methodDescriptor = m::mock('phpDocumentor\Descriptor\MethodDescriptor');
        $constantDescriptor = m::mock('phpDocumentor\Descriptor\ConstantDescriptor');
        $namespaceDescriptor = m::mock('phpDocumentor\Descriptor\NamespaceDescriptor');
        $namespaceDescriptor->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('phpDocumentor');
        $interface = $this->createInterfaceDescriptorMock();
        $interface->shouldReceive('getParent')->andReturn([]);
        $interface->shouldReceive('getMethods')->andReturn([$methodDescriptor]);
        $interface->shouldReceive('getConstants')->andReturn([$constantDescriptor]);
        $interface->shouldReceive('getNamespace')->andReturn($namespaceDescriptor);
        $parent = $this->prepareParentXMLElement();
        $interfaceConverter = $this->createFixture($interface, $methodDescriptor, $constantDescriptor);

        // Act
        $convertedElement = $interfaceConverter->convert($parent, $interface);

        // Assert
        $this->assertSame('100', $convertedElement->getAttribute('line'));
        $this->assertSame('phpDocumentor', $convertedElement->getAttribute('namespace'));
        $this->assertSame('Interface', $convertedElement->getElementsByTagName('name')->item(0)->nodeValue);
        $this->assertSame(
            'phpDocumentor\Interface',
            $convertedElement->getElementsByTagName('full_name')->item(0)->nodeValue
        );
    }

    /**
     * Creates an XML Element that can serve as parent.
     *
     * @return \DOMElement
     */
    protected function prepareParentXMLElement() : \DOMElement
    {
        $document = new \DOMDocument();
        $parent = new \DOMElement('file');
        $document->appendChild($parent);

        return $parent;
    }

    /**
     * Creates a mock for the InterfaceDescriptor class.
     *
     * @return m\MockInterface|DescriptorAbstract
     */
    protected function createInterfaceDescriptorMock()
    {
        $interface = m::mock('phpDocumentor\\Descriptor\\InterfaceDescriptor');
        $interface->shouldReceive('getLine')->andReturn(100);
        $interface->shouldReceive('getName')->andReturn('Interface');
        $interface->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('phpDocumentor\Interface');
        $interface->shouldIgnoreMissing();

        return $interface;
    }

    /**
     * Creates the InterfaceConverter fixture with a DocBlock mock.
     *
     * @param MethodDescriptor $method
     * @param ConstantDescriptor $constant
     * @return InterfaceConverter
     */
    protected function createFixture(InterfaceDescriptor $interface, $method, $constant) : InterfaceConverter
    {
        $docBlockConverter = m::mock('phpDocumentor\Transformer\Writer\Xml\DocBlockConverter');
        $docBlockConverter->shouldReceive('convert')->with(m::type('DOMElement'), $interface);

        $methodConverter = m::mock('phpDocumentor\Transformer\Writer\Xml\MethodConverter');
        $methodConverter->shouldReceive('convert')->with(m::type('DOMElement'), $method);

        $constantConverter = m::mock('phpDocumentor\Transformer\Writer\Xml\ConstantConverter');
        $constantConverter->shouldReceive('convert')->with(m::type('DOMElement'), $constant);

        return new InterfaceConverter($docBlockConverter, $methodConverter, $constantConverter);
    }
}
