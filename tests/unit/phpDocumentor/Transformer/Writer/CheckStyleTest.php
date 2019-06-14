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

use Mockery as m;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Test class for \phpDocumentor\Transformer\Writer\Checkstyle.
 *
 * @covers \phpDocumentor\Transformer\Writer\Checkstyle
 */
class CheckStyleTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var Checkstyle
     */
    protected $checkStyle;

    /** @var vfsStreamDirectory */
    private $fs;

    /**
     * Sets up the test suite
     */
    protected function setUp()
    {
        $this->checkStyle = new Checkstyle();
        $this->fs = vfsStream::setup('CheckStyleTest');
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\Checkstyle::transform
     */
    public function testTransform()
    {
        $transformer = m::mock('phpDocumentor\Transformer\Transformation');
        $transformer->shouldReceive('getTransformer->getTarget')->andReturn(vfsStream::url('CheckStyleTest'));
        $transformer->shouldReceive('getArtifact')->andReturn('artifact.xml');

        $fileDescriptor = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $projectDescriptor = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $projectDescriptor->shouldReceive('getFiles->getAll')->andReturn([$fileDescriptor]);

        $error = m::mock('phpDocumentor\Descriptor\Validator\Error');
        $fileDescriptor->shouldReceive('getPath')->andReturn('/foo/bar/baz');
        $fileDescriptor->shouldReceive('getAllErrors->getAll')->andReturn([$error]);

        $error->shouldReceive('getLine')->andReturn(1234);
        $error->shouldReceive('getCode')->andReturn(5678);
        $error->shouldReceive('getSeverity')->andReturn('error');
        $error->shouldReceive('getContext')->andReturn('myContext');

        // Call the actual method
        $this->checkStyle->transform($projectDescriptor, $transformer);

        // Assert file exists
        $this->assertTrue($this->fs->hasChild('artifact.xml'));

        // Inspect XML
        $xml = <<<XML
<?xml version="1.0"?>
<checkstyle version="1.3.0">
  <file name="/foo/bar/baz">
    <error line="1234" severity="error" message="5678 myContext" source="phpDocumentor.file.5678"/>
  </file>
</checkstyle>
XML;
        $expectedXml = new \DOMDocument();
        $expectedXml->loadXML($xml);

        $actualXml = new \DOMDocument();
        $actualXml->load(vfsStream::url('CheckStyleTest/artifact.xml'));

        $this->assertEqualXMLStructure($expectedXml->firstChild, $actualXml->firstChild, true);
    }
}
