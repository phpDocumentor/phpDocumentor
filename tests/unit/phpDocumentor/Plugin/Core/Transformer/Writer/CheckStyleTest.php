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

use Mockery as m;
use org\bovigo\vfs\vfsStream;

/**
 * Test class for \phpDocumentor\Plugin\Core\Transformer\Writer\CheckStyle.
 *
 * @covers phpDocumentor\Plugin\Core\Transformer\Writer\CheckStyle
 */
class CheckStyleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckStyle $checkstyle
     */
    protected $checkStyle;

    /**
     * Sets up the test suite
     *
     * @return void
     */
    public function setUp()
    {
        $this->translator = m::mock('phpDocumentor\Translator');
        $this->checkStyle = new CheckStyle();
        $this->checkStyle->setTranslator($this->translator);
        $this->fs = vfsStream::setup('CheckStyleTest');
    }

    /**
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\CheckStyle::transform
     */
    public function testTransform()
    {
        $transformer = m::mock('phpDocumentor\Transformer\Transformation');
        $transformer->shouldReceive('getTransformer->getTarget')->andReturn(vfsStream::url('CheckStyleTest'));
        $transformer->shouldReceive('getArtifact')->andReturn('artifact.xml');

        $fileDescriptor = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $projectDescriptor = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $projectDescriptor->shouldReceive('getFiles->getAll')->andReturn(array($fileDescriptor));

        $error = m::mock('phpDocumentor\Descriptor\Validator\Error');
        $fileDescriptor->shouldReceive('getPath')->andReturn('/foo/bar/baz');
        $fileDescriptor->shouldReceive('getErrors->getAll')->andReturn(array($error));

        $error->shouldReceive('getLine')->andReturn(1234);
        $error->shouldReceive('getCode')->andReturn(5678);
        $error->shouldReceive('getSeverity')->andReturn('error');
        $error->shouldReceive('getContext')->andReturn('myContext');

        $this->translator->shouldReceive('translate')->with('5678')->andReturn('5678 %s');

        $this->checkStyle->transform($projectDescriptor, $transformer);

        // Assert file exists
        $this->assertTrue($this->fs->hasChild('artifact.xml'));

        // Inspect XML
        $xml = simplexml_load_file(vfsStream::url('CheckStyleTest/artifact.xml'));
        $this->assertSame('/foo/bar/baz', (string) $xml->file['name']);
        $this->assertSame('1234', (string) $xml->file->error['line']);
        $this->assertSame('error', (string) $xml->file->error['severity']);
        $this->assertSame('5678 myContext', (string) $xml->file->error['message']);
        $this->assertSame('phpDocumentor.file.5678', (string) $xml->file->error['source']);
    }
}
