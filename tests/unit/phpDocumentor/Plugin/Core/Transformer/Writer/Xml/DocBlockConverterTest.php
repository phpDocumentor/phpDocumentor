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
use phpDocumentor\Transformer\Router\RouterAbstract;

/**
 * Test class for \phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter.
 */
class DocBlockConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var DocBlockConverter */
    protected $fixture;

    /** @var m\MockInterface|RouterAbstract */
    protected $routerMock;

    /** @var m\MockInterface|TagConverter */
    protected $tagConverterMock;

    /**
     * Sets up the fixture with mocked dependencies.
     */
    public function setUp()
    {
        $this->tagConverterMock  = $this->givenATagConverter();
        $this->routerMock        = $this->givenARouter();
        $this->fixture           = new DocBlockConverter($this->tagConverterMock, $this->routerMock);
    }

    /**
     * Tests whether the XML Element representing a DocBlock is properly created.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::__construct
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::convert
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::addSummary
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::addDescription
     *
     * @return void
     */
    public function testIfXmlElementForDocBlockIsCreated()
    {
        // Arrange
        $descriptor = $this->givenADescriptorWithSummaryDescriptionAndTags('summary', 'description', array());

        // Act
        $convertedElement = $this->fixture->convert($this->prepareParentXMLElement(), $descriptor);

        // Assert
        $this->assertSame('100', $convertedElement->getAttribute('line'));
        $this->assertSame('summary', $convertedElement->getElementsByTagName('description')->item(0)->nodeValue);
        $this->assertSame(
            'description',
            $convertedElement->getElementsByTagName('long-description')->item(0)->nodeValue
        );
        $this->assertSame(0, $convertedElement->getElementsByTagName('tag')->length);
    }

    /**
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::convert
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::addTags
     */
    public function testParentPackageIsSetByDocBlocksPackage()
    {
        // Arrange
        $parent = $this->prepareParentXMLElement();
        $descriptor = $this->givenADescriptorWithSummaryDescriptionAndTags('summary', 'description', array(null));

        // Act
        $this->fixture->convert($parent, $descriptor);

        // Assert
        $this->assertSame('This\Is\A\Package', $parent->getAttribute('package'));
    }

    /**
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::convert
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::addTags
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::addInheritedFromTag
     */
    public function testConvertTagsOntoDocBlock()
    {
        // Arrange
        $parent = $this->prepareParentXMLElement();
        $tag = m::mock('phpDocumentor\Descriptor\TagDescriptor');
        $this->tagConverterMock->shouldReceive('convert')->with(m::type('DOMElement'), $tag);

        $descriptor = $this->givenADescriptorWithSummaryDescriptionAndTags(
            'summary',
            'description',
            array(array($tag))
        );

        // Act
        $this->fixture->convert($parent, $descriptor);

        // Assert
        $this->assertTrue(true);
    }

    /**
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::convert
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml\DocBlockConverter::addInheritedFromTag
     */
    public function testAddInheritedFromTag()
    {
        // Arrange
        $fqcn   = 'fqcn';
        $url    = 'url';
        $parent = $this->prepareParentXMLElement();

        $parentDescriptor = $this->givenADescriptor()
            ->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn($fqcn)
            ->getMock();

        $descriptor = $this->givenADescriptorWithSummaryDescriptionAndTags('summary', 'description', array())
            ->shouldReceive('getInheritedElement')->andReturn($parentDescriptor)
            ->getMock();

        $ruleMock = $this->givenARuleThatGeneratesTheGivenUrl($url);
        $this->routerMock->shouldReceive('match')->with($parentDescriptor)->andReturn($ruleMock);

        // Act
        $this->fixture->convert($parent, $descriptor);

        // Assert
        $this->assertSame('inherited_from', $parent->getElementsByTagName('tag')->item(0)->getAttribute('name'));
        $this->assertSame($fqcn, $parent->getElementsByTagName('tag')->item(0)->getAttribute('refers'));
        $this->assertSame($fqcn, $parent->getElementsByTagName('tag')->item(0)->getAttribute('description'));
        $this->assertSame($url, $parent->getElementsByTagName('tag')->item(0)->getAttribute('link'));
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
    protected function givenADescriptor()
    {
        $tag = m::mock('phpDocumentor\\Descriptor\\DescriptorAbstract');
        $tag->shouldReceive('getLine')->andReturn(100);
        $tag->shouldReceive('getPackage')->andReturn('This\Is\A\Package');
        $tag->shouldIgnoreMissing();

        return $tag;
    }

    /**
     * Returns a tag converter mock.
     *
     * @return m\MockInterface|TagConverter
     */
    protected function givenATagConverter()
    {
        return m::mock('phpDocumentor\Plugin\Core\Transformer\Writer\Xml\TagConverter');
    }

    /**
     * Returns a router mock.
     *
     * @return m\MockInterface|RouterAbstract
     */
    protected function givenARouter()
    {
        return m::mock('phpDocumentor\Transformer\Router\RouterAbstract');
    }

    /**
     * Returns a mock for a descriptor, including summary, description and tags.
     *
     * @param string $summary
     * @param string $description
     * @param array  $tags
     *
     * @return m\MockInterface|DescriptorAbstract
     */
    protected function givenADescriptorWithSummaryDescriptionAndTags($summary, $description, $tags)
    {
        $descriptor = $this->givenADescriptor();
        $this->whenDescriptorHasTags($descriptor, $tags);
        $this->whenDescriptorHasSummary($descriptor, $summary);
        $this->whenDescriptorHasDescription($descriptor, $description);

        return $descriptor;
    }

    /**
     * Describes when a descriptor has a summary.
     *
     * @param DescriptorAbstract|m\MockInterface $descriptor
     * @param string                             $summary
     *
     * @return void
     */
    protected function whenDescriptorHasSummary($descriptor, $summary)
    {
        $descriptor->shouldReceive('getSummary')->andReturn($summary);
    }

    /**
     * Describes when a descriptor has a description.
     *
     * @param DescriptorAbstract|m\MockInterface $descriptor
     * @param string                             $description
     *
     * @return void
     */
    protected function whenDescriptorHasDescription($descriptor, $description)
    {
        $descriptor->shouldReceive('getDescription')->andReturn($description);
    }

    /**
     * Describes when a descriptor has tags.
     *
     * @param DescriptorAbstract|m\MockInterface $descriptor
     * @param array                              $tags
     *
     * @return void
     */
    protected function whenDescriptorHasTags($descriptor, $tags)
    {
        $descriptor->shouldReceive('getTags')->andReturn($tags);
    }

    /**
     * Returns a mock for a Rule and generates the given URL.
     *
     * @param string $url
     *
     * @return m\MockInterface
     */
    protected function givenARuleThatGeneratesTheGivenUrl($url)
    {
        $ruleMock = m::mock('phpDocumentor\Transformer\Router\Rule');
        $ruleMock->shouldReceive('generate')->andReturn($url);

        return $ruleMock;
    }
}
