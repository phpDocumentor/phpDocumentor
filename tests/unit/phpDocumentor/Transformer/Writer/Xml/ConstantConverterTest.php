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

/**
 * Test class for \phpDocumentor\Transformer\Writer\Xml\ConstantConverter.
 *
 * @covers \phpDocumentor\Transformer\Writer\Xml\ConstantConverter
 */
class ConstantConverterTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * Tests whether the XML Element representing a constant is properly created.
     *
     * @covers \phpDocumentor\Transformer\Writer\Xml\ConstantConverter::convert
     */
    public function testConstantXmlElementIsCreated() : void
    {
        // Arrange
        $constant = $this->createConstantDescriptorMock();
        $constantConverter = $this->createFixture($constant);
        $parent = $this->prepareParentXMLElement();
        $parent->setAttribute('namespace', 'phpDocumentor');

        // Act
        $convertedElement = $constantConverter->convert($parent, $constant);

        // Assert
        $this->assertSame('100', $convertedElement->getAttribute('line'));
        $this->assertSame('phpDocumentor', $convertedElement->getAttribute('namespace'));
        $this->assertSame('Constant', $convertedElement->getElementsByTagName('name')->item(0)->nodeValue);
        $this->assertSame(
            'phpDocumentor\Class::Constant',
            $convertedElement->getElementsByTagName('full_name')->item(0)->nodeValue
        );
        $this->assertSame('defaultString', $convertedElement->getElementsByTagName('value')->item(0)->nodeValue);
    }

    /**
     * Tests whether the XML Element representing a constant is properly created.
     *
     * @covers \phpDocumentor\Transformer\Writer\Xml\ConstantConverter::convert
     */
    public function testNamespaceNameIsTakenFromNamespaceDescriptorIfPresent() : void
    {
        // Arrange
        $parent = $this->prepareParentXMLElement();
        $namespaceDescriptor = m::mock('phpDocumentor\Descriptor\NamespaceDescriptor');
        $namespaceDescriptor->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('MySpace');
        $constant = $this->createConstantDescriptorMock();
        $constant->shouldReceive('getNamespace')->andReturn($namespaceDescriptor);
        $constantConverter = $this->createFixture($constant);

        // Act
        $convertedElement = $constantConverter->convert($parent, $constant);

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
     * Creates a mock for the ConstantDescriptor class.
     *
     * @return m\MockInterface|DescriptorAbstract
     */
    protected function createConstantDescriptorMock()
    {
        $constant = m::mock('phpDocumentor\\Descriptor\\ConstantDescriptor');
        $constant->shouldReceive('getLine')->andReturn(100);
        $constant->shouldReceive('getName')->andReturn('Constant');
        $constant->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('phpDocumentor\Class::Constant');
        $constant->shouldReceive('getValue')->andReturn('defaultString');
        $constant->shouldIgnoreMissing();

        return $constant;
    }

    /**
     * Creates the ConstantConverter fixture with a DocBlock mock.
     *
     * @return ConstantConverter
     */
    protected function createFixture(ConstantDescriptor $constant) : ConstantConverter
    {
        $docBlockConverter = m::mock('phpDocumentor\Transformer\Writer\Xml\DocBlockConverter');
        $docBlockConverter->shouldReceive('convert')->with(m::type('DOMElement'), $constant);

        return new ConstantConverter($docBlockConverter);
    }
}
