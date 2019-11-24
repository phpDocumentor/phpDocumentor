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
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;

/**
 * Test class for \phpDocumentor\Transformer\Writer\Xml\TagConverter.
 *
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Xml\TagConverter
 */
class TagConverterTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    const TEST_LINENUMBER = 100;

    /**
     * Tests whether the information common to all tags is stored on an XML element.
     *
     * @param string $name              Name of the tag as provided by the Descriptor.
     * @param string $description       Description for the tag as provided by the Descriptor.
     * @param string $resultName        Expected resulting name in the XML Element.
     * @param string $resultDescription Expected resulting description in the XML Element.
     *
     * @dataProvider provideTestGenericTag
     * @covers ::convert
     * @covers ::getDescription
     */
    public function testConvertGenericTag($name, $description, $resultName, $resultDescription) : void
    {
        // Arrange
        $tagConverter = new TagConverter();
        $parent = $this->prepareDocBlockXMLElement();
        $tag = $this->createTagDescriptorMock($name, $description);

        // Act
        $convertedElement = $tagConverter->convert($parent, $tag);

        // Assert
        $this->assertSame($resultName, $convertedElement->getAttribute('name'));
        $this->assertSame($resultDescription, $convertedElement->getAttribute('description'));
        $this->assertSame((string) self::TEST_LINENUMBER, $convertedElement->getAttribute('line'));
    }

    /**
     * Tests whether type information is stored when a tag is processed with compound type information.
     *
     * @covers ::convert
     * @covers ::addTypes
     */
    public function testWhetherTypesAreAddedWhenPresentAndTypeIsCompound() : void
    {
        // Arrange
        $tagConverter = new TagConverter();
        $parent = $this->prepareDocBlockXMLElement();
        $tag = $this->createTagDescriptorMock('name', 'description', 'Tag\VarDescriptor');
        $tag->shouldReceive('getType')->andReturn(
            new Compound([new String_(), new Integer(), new Object_(new Fqsen('\DateTime'))])
        );

        // Act
        $convertedElement = $tagConverter->convert($parent, $tag);

        // Assert
        $types = $convertedElement->getElementsByTagName('type');
        $this->assertSame(3, $types->length);
        $this->assertSame('string', $types->item(0)->nodeValue);
        $this->assertSame('int', $types->item(1)->nodeValue);
        $this->assertSame('\DateTime', $types->item(2)->nodeValue);
        $this->assertSame('string|int|\DateTime', $convertedElement->getAttribute('type'));
    }

    /**
     * Tests whether type information is stored when a tag is processed with information of a single type.
     *
     * @covers ::convert
     * @covers ::addTypes
     */
    public function testWhetherTypesAreAddedWhenPresentAndTypeIsSingular() : void
    {
        // Arrange
        $tagConverter = new TagConverter();
        $parent = $this->prepareDocBlockXMLElement();
        $tag = $this->createTagDescriptorMock('name', 'description', 'Tag\VarDescriptor');
        $tag->shouldReceive('getType')->andReturn(new String_());

        // Act
        $convertedElement = $tagConverter->convert($parent, $tag);

        // Assert
        $types = $convertedElement->getElementsByTagName('type');
        $this->assertSame(1, $types->length);
        $this->assertSame('string', $convertedElement->getAttribute('type'));
    }

    /**
     * Tests whether the variable name is stored for tags containing variable names.
     *
     * @covers ::convert
     * @covers ::addTypes
     */
    public function testWhetherVariableNamesAreAddedWhenPresent() : void
    {
        // Arrange
        $tagConverter = new TagConverter();
        $parent = $this->prepareDocBlockXMLElement();
        $tag = $this->createTagDescriptorMock('name', 'description', 'Tag\VarDescriptor');
        $tag->shouldReceive('getType')->andReturn(null);
        $tag->shouldReceive('getVariableName')->andReturn('varName');

        // Act
        $convertedElement = $tagConverter->convert($parent, $tag);

        // Assert
        $this->assertSame('varName', $convertedElement->getAttribute('variable'));
    }

    /**
     * Tests whether the version number is prepended to the description when version information is available.
     *
     * @covers ::convert
     * @covers ::getDescription
     *
     * @todo this should be dealt with in a template and not in the code! This activity should be removed and the
     * templates updated.
     */
    public function testWhetherTheVersionIsPrependedToTheDescription() : void
    {
        // Arrange
        $tagConverter = new TagConverter();
        $parent = $this->prepareDocBlockXMLElement();
        $tag = $this->createTagDescriptorMock('name', 'description', 'Tag\VersionDescriptor');
        $tag->shouldReceive('getVersion')->andReturn('1.0');

        // Act
        $convertedElement = $tagConverter->convert($parent, $tag);

        // Assert
        $this->assertSame('1.0 description', $convertedElement->getAttribute('description'));
    }

    /**
     * Tests whether a reference to another element is stored with the tag when such is present.
     *
     * @covers ::convert
     */
    public function testWhetherReferencesAreAddedWhenPresent() : void
    {
        // Arrange
        $reference = '\DateTime::add()';
        $tagConverter = new TagConverter();
        $parent = $this->prepareDocBlockXMLElement();
        $tag = $this->createTagDescriptorMock('name', 'description', 'Tag\UsesDescriptor');
        $tag->shouldReceive('getReference')->andReturn($reference);

        // Act
        $convertedElement = $tagConverter->convert($parent, $tag);

        // Assert
        $this->assertSame($reference, $convertedElement->getAttribute('link'));
    }

    /**
     * Tests whether a link to a URL is stored with the tag when such is present.
     *
     * @covers ::convert
     */
    public function testWhetherLinksAreAddedWhenPresent() : void
    {
        // Arrange
        $link = 'http://www.phpdoc.org';
        $tagConverter = new TagConverter();
        $parent = $this->prepareDocBlockXMLElement();
        $tag = $this->createTagDescriptorMock('name', 'description', 'Tag\LinkDescriptor');
        $tag->shouldReceive('getLink')->andReturn($link);

        // Act
        $convertedElement = $tagConverter->convert($parent, $tag);

        // Assert
        $this->assertSame($link, $convertedElement->getAttribute('link'));
    }

    /**
     * Tests whether a method name to another element is stored with the tag when such is present.
     *
     * @covers ::convert
     */
    public function testWhetherMethodNamesAreAddedWhenPresent() : void
    {
        // Arrange
        $methodName = 'getMethod';
        $tagConverter = new TagConverter();
        $parent = $this->prepareDocBlockXMLElement();
        $tag = $this->createTagDescriptorMock('name', 'description', 'Tag\MethodDescriptor');
        $tag->shouldReceive('getMethodName')->andReturn($methodName);

        // Act
        $convertedElement = $tagConverter->convert($parent, $tag);

        // Assert
        $this->assertSame($methodName, $convertedElement->getAttribute('method_name'));
    }

    /**
     * Provides a test name and description for the generic test.
     *
     * @see testConvertGenericTag
     *
     * @return string[][]
     */
    public function provideTestGenericTag() : array
    {
        return [
            ['name', 'description', 'name', 'description'],
            ['name&test', 'description&test', 'name&amp;test', 'description&amp;test'],
        ];
    }

    /**
     * Creates an XML Element that can serve as parent.
     *
     * @return \DOMElement
     */
    protected function prepareDocBlockXMLElement() : \DOMElement
    {
        $document = new \DOMDocument();
        $parent = new \DOMElement('parent');
        $document->appendChild($parent);
        $parent->setAttribute('line', self::TEST_LINENUMBER);

        return $parent;
    }

    /**
     * Creates a mock for the TagDescriptor class.
     *
     * @param string $name        The name of the tag.
     * @param string $description The description that is present in the tag.
     * @param string $class       The descriptor class that is to be mocked
     *
     * @return m\MockInterface|TagDescriptor
     */
    protected function createTagDescriptorMock($name, $description, $class = 'TagDescriptor')
    {
        $tag = m::mock('phpDocumentor\\Descriptor\\' . $class);
        $tag->shouldReceive('getName')->andReturn($name);
        $tag->shouldReceive('getDescription')->andReturn($description);
        $tag->shouldIgnoreMissing();

        return $tag;
    }
}
