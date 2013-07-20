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

use Mockery as m;
use org\bovigo\vfs\vfsStream;

/**
 * Test class for \phpDocumentor\Plugin\Core\Transformer\Writer\Xml.
 *
 * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml
 */
class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Xml $xml
     */
    protected $xml;

    /**
     * Sets up the test suite
     *
     * @return void
     */
    public function setUp()
    {
        $this->translator = m::mock('phpDocumentor\Translator');
        $this->projectDescriptor = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $this->xml = new Xml();
        $this->xml->setTranslator($this->translator);
        $this->fs = vfsStream::setup('XmlTest');
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

        $this->implementProtectedFinalize($this->projectDescriptor);

        $this->xml->transform($this->projectDescriptor, $transformation);

        // Check file exists
        $this->assertTrue($this->fs->hasChild('artifact.xml'));

        // Inspect XML
        $xml = simplexml_load_file(vfsStream::url('XmlTest/artifact.xml'));
        $this->assertSame('0', (string) $xml->deprecated['count']);
    }

    /**
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Xml::transform
     */
    public function testTransform()
    {
        $this->markTestIncomplete();

        $transformer = m::mock('phpDocumentor\Transformer\Transformer');
        $transformer->shouldReceive('getTarget')->andReturn(vfsStream::url('XmlTest'));

        $transformation = m::mock('phpDocumentor\Transformer\Transformation');
        $transformation->shouldReceive('getArtifact')->andReturn('artifact.xml');
        $transformation->shouldReceive('getTransformer')->andReturn($transformer);

        $fileDescriptor = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $fileDescriptor->shouldReceive('getPath')->andReturn('foo.php');
        $transformer->shouldReceive('generateFilename')->with('foo.php')->andReturn('generated-foo.php');
        $fileDescriptor->shouldReceive('getHash')->andReturn(sha1(''));

        $this->projectDescriptor->shouldReceive('getFiles')->andReturn(array($fileDescriptor));

        $this->implementProtectedFinalize($this->projectDescriptor);

        $this->xml->transform($this->projectDescriptor, $transformation);

        // Check file exists
        $this->assertTrue($this->fs->hasChild('artifact.xml'));

        // Inspect XML
        $xml = simplexml_load_file(vfsStream::url('XmlTest/artifact.xml'));
        $this->assertSame('0', (string) $xml->deprecated['count']);
    }

    /**
     * This implements testing of the protected finalize method.
     *
     * @param ProjectDescriptor $projectDescriptor
     * @return voidasdasda
     */
    protected function implementProtectedFinalize(ProjectDescriptor $projectDescriptor)
    {
        $this->projectDescriptor->shouldReceive('isVisibilityAllowed')
            ->with(ProjectDescriptor\Settings::VISIBILITY_INTERNAL)
            ->andReturn(true);
    }
}
