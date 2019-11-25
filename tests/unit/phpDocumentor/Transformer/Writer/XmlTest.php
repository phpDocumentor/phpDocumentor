<?php

/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use DOMDocument;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use org\bovigo\vfs\vfsStream;

use org\bovigo\vfs\vfsStreamDirectory;
use phpDocumentor\Descriptor\Collection as DescriptorCollection;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Router\RouterAbstract;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;

/**
 * Test class for \phpDocumentor\Transformer\Writer\Xml.
 *
 * @covers \phpDocumentor\Transformer\Writer\Xml
 */
class XmlTest extends MockeryTestCase
{
    /** @var Xml $xml */
    protected $xml;

    /** @var m\MockInterface|RouterAbstract */
    protected $routerMock;

    /** @var m\MockInterface|ProjectDescriptor */
    protected $projectDescriptor;

    /** @var vfsStreamDirectory */
    protected $fs;

    /**
     * Sets up the test suite
     */
    protected function setUp(): void
    {
        $this->fs = vfsStream::setup('XmlTest');
        $this->projectDescriptor = m::mock(ProjectDescriptor::class);
        $this->routerMock = m::mock(RouterAbstract::class);
        $this->xml = new Xml($this->routerMock);
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Xml::transform
     */
    public function testTransformWithoutFiles() : void
    {
        $transformer = m::mock(Transformer::class);
        $transformation = m::mock(Transformation::class);
        $transformation->shouldReceive('getTransformer->getTarget')->andReturn(vfsStream::url('XmlTest'));
        $transformation->shouldReceive('getArtifact')->andReturn('artifact.xml');
        $transformation->shouldReceive('getTransformer')->andReturn($transformer);

        $this->projectDescriptor->shouldReceive('getFiles')->andReturn(new DescriptorCollection());
        $this->projectDescriptor->shouldReceive('getName')->andReturn('project');
        $this->projectDescriptor->shouldReceive('getPartials')->andReturn(new DescriptorCollection());

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
        $expectedXml = new DOMDocument();
        $expectedXml->loadXML($xml);

        $actualXml = new DOMDocument();
        $actualXml->load(vfsStream::url('XmlTest/artifact.xml'));

        $this->assertEqualXMLStructure($expectedXml->firstChild, $actualXml->firstChild, true);
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Xml::transform
     */
    public function testTransformWithEmptyFileDescriptor() : void
    {
        $transformer = m::mock(Transformer::class);
        $transformer->shouldReceive('getTarget')->andReturn(vfsStream::url('XmlTest'));

        $transformation = m::mock(Transformation::class);
        $transformation->shouldReceive('getArtifact')->andReturn('artifact.xml');
        $transformation->shouldReceive('getTransformer')->andReturn($transformer);

        $fileDescriptor = m::mock(FileDescriptor::class);
        $fileDescriptor->shouldReceive('getPath')->andReturn('foo.php');
        $fileDescriptor->shouldReceive('getInheritedElement')->andReturn(null);
        $transformer->shouldReceive('generateFilename')->with('foo.php')->andReturn('generated-foo.php');
        $fileDescriptor->shouldReceive('getHash')->andReturn('hash');
        $fileDescriptor->shouldReceive('getAllErrors')->andReturn([]);

        $this->projectDescriptor->shouldReceive('getFiles')->andReturn(new DescriptorCollection([$fileDescriptor]));
        $this->projectDescriptor->shouldReceive('getName')->andReturn('project');
        $this->projectDescriptor->shouldReceive('getPartials')->andReturn(new DescriptorCollection());

        $this->implementProtectedFinalize($this->projectDescriptor);
        $this->implementProtectedBuildDocBlock($fileDescriptor);

        $fileDescriptor->shouldReceive('getNamespaceAliases')->andReturn(['foo', 'bar']);
        $fileDescriptor->shouldReceive('getConstants')->andReturn(new DescriptorCollection());
        $fileDescriptor->shouldReceive('getFunctions')->andReturn(new DescriptorCollection());
        $fileDescriptor->shouldReceive('getInterfaces')->andReturn(new DescriptorCollection());
        $fileDescriptor->shouldReceive('getClasses')->andReturn(new DescriptorCollection());
        $fileDescriptor->shouldReceive('getTraits')->andReturn(new DescriptorCollection());
        $fileDescriptor->shouldReceive('getMarkers')->andReturn(new DescriptorCollection());
        $fileDescriptor->shouldReceive('getErrors')->andReturn(new DescriptorCollection());
        $fileDescriptor->shouldReceive('getPartials')->andReturn(new DescriptorCollection());
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
        $expectedXml = new DOMDocument();
        $expectedXml->loadXML($xml);

        $actualXml = new DOMDocument();
        $actualXml->load(vfsStream::url('XmlTest/artifact.xml'));

        $this->assertEqualXMLStructure($expectedXml->firstChild, $actualXml->firstChild, true);
    }

    /**
     * This implements testing of the protected finalize method.
     */
    protected function implementProtectedFinalize(ProjectDescriptor $projectDescriptor) : void
    {
        $this->projectDescriptor->shouldReceive('isVisibilityAllowed')
            ->with(ProjectDescriptor\Settings::VISIBILITY_INTERNAL)
            ->andReturn(true);
    }

    /**
     * This implements testing of the protected buildDocBlock method
     */
    protected function implementProtectedBuildDocBlock(m\MockInterface $descriptor) : void
    {
        $descriptor->shouldReceive('getLine')->andReturn(666);
        $descriptor->shouldReceive('getPackage')->andReturn('myPackage');
        $descriptor->shouldReceive('getSummary')->andReturn('my summary');
        $descriptor->shouldReceive('getDescription')->andReturn('my description');
        $descriptor->shouldReceive('getTags')->andReturn([]);
    }
}
