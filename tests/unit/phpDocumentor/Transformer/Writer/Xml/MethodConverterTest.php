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
use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\MethodDescriptor;

/**
 * Test class for \phpDocumentor\Transformer\Writer\Xml\MethodConverter.
 *
 * @covers \phpDocumentor\Transformer\Writer\Xml\MethodConverter
 */
class MethodConverterTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * Tests whether the XML Element representing a method is properly created.
     *
     * @covers \phpDocumentor\Transformer\Writer\Xml\MethodConverter::convert
     */
    public function testMethodXmlElementIsCreated() : void
    {
        // Arrange
        $parent = $this->prepareParentXMLElement();
        $parent->setAttribute('namespace', 'phpDocumentor');
        $argumentDescriptor = m::mock('phpDocumentor\Descriptor\ArgumentDescriptor');
        $method = $this->createMethodDescriptorMock();
        $method->shouldReceive('getArguments')->andReturn([$argumentDescriptor]);
        $methodConverter = $this->createFixture($method, $argumentDescriptor);

        // Act
        $convertedElement = $methodConverter->convert($parent, $method);

        // Assert
        $this->assertSame('false', $convertedElement->getAttribute('final'));
        $this->assertSame('true', $convertedElement->getAttribute('abstract'));
        $this->assertSame('false', $convertedElement->getAttribute('static'));
        $this->assertSame('protected', $convertedElement->getAttribute('visibility'));
        $this->assertSame('100', $convertedElement->getAttribute('line'));
        $this->assertSame('phpDocumentor', $convertedElement->getAttribute('namespace'));
        $this->assertSame('Method', $convertedElement->getElementsByTagName('name')->item(0)->nodeValue);
        $this->assertSame(
            'phpDocumentor\Class::Method()',
            $convertedElement->getElementsByTagName('full_name')->item(0)->nodeValue
        );
    }

    /**
     * Tests whether the XML Element representing a method is properly created.
     *
     * @covers \phpDocumentor\Transformer\Writer\Xml\MethodConverter::convert
     */
    public function testNamespaceNameIsTakenFromNamespaceDescriptorIfPresent() : void
    {
        // Arrange
        $parent = $this->prepareParentXMLElement();
        $namespaceDescriptor = m::mock('phpDocumentor\Descriptor\NamespaceDescriptor');
        $namespaceDescriptor->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('MySpace');
        $method = $this->createMethodDescriptorMock();
        $method->shouldReceive('getArguments')->andReturn([]);
        $method->shouldReceive('getNamespace')->andReturn($namespaceDescriptor);
        $methodConverter = $this->createFixture($method);

        // Act
        $convertedElement = $methodConverter->convert($parent, $method);

        // Assert
        $this->assertSame('MySpace', $convertedElement->getAttribute('namespace'));
    }

    /**
     * Creates an XML Element that can serve as parent.
     *
     * @return \DOMElement
     */
    protected function prepareParentXMLElement() : \DOMElement
    {
        $document = new \DOMDocument();
        $parent = new \DOMElement('class');
        $document->appendChild($parent);

        return $parent;
    }

    /**
     * Creates a mock for the MethodDescriptor class.
     *
     * @return m\MockInterface|DescriptorAbstract
     */
    protected function createMethodDescriptorMock()
    {
        $method = m::mock('phpDocumentor\\Descriptor\\MethodDescriptor');
        $method->shouldReceive('getLine')->andReturn(100);
        $method->shouldReceive('isFinal')->andReturn(false);
        $method->shouldReceive('isAbstract')->andReturn(true);
        $method->shouldReceive('isStatic')->andReturn(false);
        $method->shouldReceive('getVisibility')->andReturn('protected');
        $method->shouldReceive('getName')->andReturn('Method');
        $method->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('phpDocumentor\Class::Method()');
        $method->shouldIgnoreMissing();

        return $method;
    }

    /**
     * Creates the MethodConverter fixture with a DocBlock and ArgumentConverter mock.
     *
     * @return MethodConverter
     */
    protected function createFixture(MethodDescriptor $method, ArgumentDescriptor $argumentDescriptor = null) : MethodConverter
    {
        $docBlockConverter = m::mock('phpDocumentor\Transformer\Writer\Xml\DocBlockConverter');
        $docBlockConverter->shouldReceive('convert')->with(m::type('DOMElement'), $method);
        $argumentConverter = m::mock('phpDocumentor\Transformer\Writer\Xml\ArgumentConverter');
        if ($argumentDescriptor) {
            $argumentConverter->shouldReceive('convert')->with(m::type('DOMElement'), $argumentDescriptor);
        }

        return new MethodConverter($argumentConverter, $docBlockConverter);
    }
}
