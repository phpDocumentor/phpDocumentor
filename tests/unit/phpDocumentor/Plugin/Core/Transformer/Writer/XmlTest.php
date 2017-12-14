<?php

/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;

use Mockery as m;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Transformer\Router\RouterAbstract;
use phpDocumentor\Translator\Translator;

/**
 * Test class for \phpDocumentor\Plugin\Core\Transformer\Writer\Xml.
 *
 * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml
 */
class XmlTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var Xml $xml */
    protected $xml;

    /** @var m\MockInterface|RouterAbstract */
    protected $routerMock;

    /** @var m\MockInterface|Translator */
    private $translator;

    /** @var m\MockInterface|ProjectDescriptor */
    protected $projectDescriptor;

    /** @var vfsStream */
    protected $fs;

    /**
     * Sets up the test suite
     *
     * @return void
     */
    public function setUp()
    {
        $this->fs                = vfsStream::setup('XmlTest');
        $this->translator        = m::mock('phpDocumentor\Translator\Translator');
        $this->projectDescriptor = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $this->routerMock        = m::mock('phpDocumentor\Transformer\Router\RouterAbstract');
        $this->xml               = new Xml($this->routerMock);
        $this->xml->setTranslator($this->translator);
    }

    /**
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml::transform
     */
    public function testTransformWithoutFiles()
    {
        $transformer = m::mock('phpDocumentor\Transformer\Transformer');
        $transformation = m::mock('phpDocumentor\Transformer\Transformation');
        $transformation->shouldReceive('getTransformer->getTarget')->andReturn(vfsStream::url('XmlTest'));
        $transformation->shouldReceive('getArtifact')->andReturn('artifact.xml');
        $transformation->shouldReceive('getTransformer')->andReturn($transformer);

        $this->projectDescriptor->shouldReceive('getFiles')->andReturn(array());
        $this->projectDescriptor->shouldReceive('getName')->andReturn('project');
        $this->projectDescriptor->shouldReceive('getPartials')->andReturn(array());

        $this->implementProtectedFinalize($this->projectDescriptor);

        // Call the actual method
        $this->xml->transform($this->projectDescriptor, $transformation);

        // Check file exists
        $this->assertTrue($this->fs->hasChild('artifact.xml'));

        // Inspect XML
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<project version="2.0.0b8&#10;" title="project">
  <partials/>
  <package name="global" full_name="global"/>
  <deprecated count="0"/>
</project>
XML;
        $expectedXml = new \DOMDocument;
        $expectedXml->loadXML($xml);

        $actualXml = new \DOMDocument;
        $actualXml->load(vfsStream::url('XmlTest/artifact.xml'));

        $this->assertEqualXMLStructure($expectedXml->firstChild, $actualXml->firstChild, true);
    }

    /**
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml::transform
     */
    public function testTransformWithEmptyFileDescriptor()
    {
        $transformer = m::mock('phpDocumentor\Transformer\Transformer');
        $transformer->shouldReceive('getTarget')->andReturn(vfsStream::url('XmlTest'));

        $transformation = m::mock('phpDocumentor\Transformer\Transformation');
        $transformation->shouldReceive('getArtifact')->andReturn('artifact.xml');
        $transformation->shouldReceive('getTransformer')->andReturn($transformer);

        $fileDescriptor = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $fileDescriptor->shouldReceive('getPath')->andReturn('foo.php');
        $fileDescriptor->shouldReceive('getInheritedElement')->andReturn(null);
        $transformer->shouldReceive('generateFilename')->with('foo.php')->andReturn('generated-foo.php');
        $fileDescriptor->shouldReceive('getHash')->andReturn('hash');
        $fileDescriptor->shouldReceive('getAllErrors')->andReturn(array());

        $this->projectDescriptor->shouldReceive('getFiles')->andReturn(array($fileDescriptor));
        $this->projectDescriptor->shouldReceive('getName')->andReturn('project');
        $this->projectDescriptor->shouldReceive('getPartials')->andReturn(array());

        $this->implementProtectedFinalize($this->projectDescriptor);
        $this->implementProtectedBuildDocBlock($fileDescriptor);

        $fileDescriptor->shouldReceive('getNamespaceAliases')->andReturn(array('foo', 'bar'));
        $fileDescriptor->shouldReceive('getConstants')->andReturn(array());
        $fileDescriptor->shouldReceive('getFunctions')->andReturn(array());
        $fileDescriptor->shouldReceive('getInterfaces')->andReturn(array());
        $fileDescriptor->shouldReceive('getClasses')->andReturn(array());
        $fileDescriptor->shouldReceive('getTraits')->andReturn(array());
        $fileDescriptor->shouldReceive('getMarkers')->andReturn(array());
        $fileDescriptor->shouldReceive('getErrors')->andReturn(array());
        $fileDescriptor->shouldReceive('getPartials')->andReturn(array());
        $fileDescriptor->shouldReceive('getSource')->andReturn(null);

        // Call the actual method
        $this->xml->transform($this->projectDescriptor, $transformation);

        // Check file exists
        $this->assertTrue($this->fs->hasChild('artifact.xml'));

        // Inspect XML
        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<project version="2.0.0b8" title="project">
  <partials/>
  <file path="foo.php" generated-path="generated-foo.php" hash="hash" package="myPackage">
    <docblock line="666">
      <description>my summary</description>
      <long-description>my description</long-description>
    </docblock>
    <namespace-alias name="0">foo</namespace-alias>
    <namespace-alias name="1">bar</namespace-alias>
  </file>
  <package name="global" full_name="global"/>
  <package name="myPackage" full_name="myPackage"/>
  <deprecated count="0"/>
</project>
XML;
        $expectedXml = new \DOMDocument;
        $expectedXml->loadXML($xml);

        $actualXml = new \DOMDocument;
        $actualXml->load(vfsStream::url('XmlTest/artifact.xml'));

        $this->assertEqualXMLStructure($expectedXml->firstChild, $actualXml->firstChild, true);
    }

    /**
     * This implements testing of the protected finalize method.
     *
     * @param ProjectDescriptor $projectDescriptor
     * @return void
     */
    protected function implementProtectedFinalize(ProjectDescriptor $projectDescriptor)
    {
        $this->projectDescriptor->shouldReceive('isVisibilityAllowed')
            ->with(ProjectDescriptor\Settings::VISIBILITY_INTERNAL)
            ->andReturn(true);
    }

    /**
     * This implements testing of the protected buildDocBlock method
     *
     * @param DescriptorAbstract $descriptor
     * @return void
     */
    protected function implementProtectedBuildDocBlock(DescriptorAbstract $descriptor)
    {
        $descriptor->shouldReceive('getLine')->andReturn(666);
        $descriptor->shouldReceive('getPackage')->andReturn('myPackage');
        $descriptor->shouldReceive('getSummary')->andReturn('my summary');
        $descriptor->shouldReceive('getDescription')->andReturn('my description');
        $descriptor->shouldReceive('getTags')->andReturn(array());
    }
}
