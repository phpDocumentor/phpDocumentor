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
use phpDocumentor\Descriptor\ConstantDescriptor;

/**
 * Test class for \phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ConstantConverter.
 *
 * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ConstantConverter
 */
class ConstantConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests whether the XML Element representing a constant is properly created.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ConstantConverter::convert
     *
     * @return void
     */
    public function testConstantXmlElementIsCreated()
    {
        // Arrange
        $constant          = $this->createConstantDescriptorMock();
        $constantConverter = $this->createFixture($constant);
        $parent            = $this->prepareParentXMLElement();
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
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\ConstantConverter::convert
     *
     * @return void
     */
    public function testNamespaceNameIsTakenFromNamespaceDescriptorIfPresent()
    {
        // Arrange
        $parent              = $this->prepareParentXMLElement();
        $namespaceDescriptor = m::mock('phpDocumentor\Descriptor\NamespaceDescriptor');
        $namespaceDescriptor->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn('MySpace');
        $constant              = $this->createConstantDescriptorMock();
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
    protected function prepareParentXMLElement()
    {
        $document = new \DOMDocument();
        $parent   = new \DOMElement('class');
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
     * @param ConstantDescriptor $constant
     *
     * @return ConstantConverter
     */
    protected function createFixture(ConstantDescriptor $constant)
    {
        $docBlockConverter = m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter');
        $docBlockConverter->shouldReceive('convert')->with(m::type('DOMElement'), $constant);

        return new ConstantConverter($docBlockConverter);
    }
}
