<?php

/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Pascal de Vink <pascal.de.vink@gmail.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use Mockery as m;
use org\bovigo\vfs\vfsStream;

/**
 * Test class for \phpDocumentor\Plugin\Core\Transformer\Writer\Statistics.
 *
 * @covers phpDocumentor\Plugin\Core\Transformer\Writer\Statistics
 */
class StatisticsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Statistics $statistics
     */
    protected $statistics;

    /**
     * Sets up the test suite
     *
     * @return void
     */
    public function setUp()
    {
        $this->statistics = new Statistics();
        $this->fs = vfsStream::setup('StatisticsTest');
    }

    public function testTransformWithArtifactAsString()
    {
        $transformer = m::mock('phpDocumentor\Transformer\Transformation');
        $transformer->shouldReceive('getTransformer->getTarget')->andReturn(vfsStream::url('StatisticsTest'));
        $transformer->shouldReceive('getArtifact')->andReturn('artifact.xml');

        $error = m::mock('phpDocumentor\Descriptor\Validator\Error');

        $fileDescriptor = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $fileDescriptor->shouldReceive('isDeprecated')->andReturn(true);
        $fileDescriptor->shouldReceive('getAllErrors->getAll')->andReturn(array($error));
        $fileDescriptor->shouldReceive('getMarkers->count')->andReturn(1);

        $projectDescriptor = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $projectDescriptor->shouldReceive('getFiles->getAll')->andReturn(array($fileDescriptor));
        $projectDescriptor->shouldReceive('getFiles->count')->andReturn(1);
        $projectDescriptor->shouldReceive('getIndexes->get')->andReturn(array($fileDescriptor));

        $this->statistics->transform($projectDescriptor, $transformer);

        // Assert file exists
        $this->assertTrue($this->fs->hasChild('artifact.xml'));

        // Inspect XML
        $now = new \DateTime('now');
        $date = $now->format(DATE_ATOM);

        $expectedXml = new \DOMDocument;
        $expectedXml->loadXML('<?xml version="1.0"?>
<phpdoc-stats version="2.6.1">
  <stat date="'.$date.'">
    <counters>
        <files>1</files>
        <deprecated>1</deprecated>
        <errors>1</errors>
        <markers>1</markers>
    </counters>
</stat>
</phpdoc-stats>');

        $actualXml = new \DOMDocument;
        $actualXml->load(vfsStream::url('StatisticsTest/artifact.xml'));

        $this->assertEqualXMLStructure($expectedXml->firstChild, $actualXml->firstChild, true);
        $this->assertSame($expectedXml->saveXML(), $actualXml->saveXML());
    }
}
