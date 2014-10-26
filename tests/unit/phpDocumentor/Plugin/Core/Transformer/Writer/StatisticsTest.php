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

    public function testTransformWithStartingArtifactAsString()
    {
        $markerCount = 1;
        $transformer = $this->givenATransformer();
        $error = $this->givenAnError();
        $fileDescriptor = $this->givenAFileDescriptor(array($error), $markerCount);
        $projectDescriptor = $this->givenAProjectDescriptor($fileDescriptor);

        $this->statistics->transform($projectDescriptor, $transformer);

        // Assert file exists
        $this->assertTrue($this->fs->hasChild('artifact.xml'));

        // Inspect XML
        $now = new \DateTime('now');
        $date = $now->format(DATE_ATOM);

        $this->thenTheXmlReportShouldContain($date, 1, 1, 1, $markerCount);
    }

    public function testTransformWithStartingArtifactAsFile()
    {
        $version = file_get_contents(__DIR__ . '/../../../../../../../VERSION');
        $statsXml = '<?xml version="1.0"?><phpdoc-stats version="' . $version . '"></phpdoc-stats>';
        vfsStream::create(array('artifact.xml' => $statsXml));

        $markerCount = 12;
        $transformer = $this->givenATransformer();
        $error = $this->givenAnError();
        $fileDescriptor = $this->givenAFileDescriptor(array($error, $error), $markerCount);
        $projectDescriptor = $this->givenAProjectDescriptor($fileDescriptor);

        $this->statistics->transform($projectDescriptor, $transformer);

        // Assert file exists
        $this->assertTrue($this->fs->hasChild('artifact.xml'));

        // Inspect XML
        $now = new \DateTime('now');
        $date = $now->format(DATE_ATOM);

        $this->thenTheXmlReportShouldContain($date, 1, 1, 2, $markerCount);
    }

    /**
     * @param $fileDescriptor
     * @return m\MockInterface
     */
    private function givenAProjectDescriptor($fileDescriptor)
    {
        $projectDescriptor = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $projectDescriptor->shouldReceive('getFiles->getAll')->andReturn(array($fileDescriptor));
        $projectDescriptor->shouldReceive('getFiles->count')->andReturn(1);
        $projectDescriptor->shouldReceive('getIndexes->get')->andReturn(array($fileDescriptor));
        return $projectDescriptor;
    }

    /**
     * @param array $errors
     * @param int   $markerCount
     * @return m\MockInterface
     */
    private function givenAFileDescriptor(array $errors, $markerCount)
    {
        $fileDescriptor = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $fileDescriptor->shouldReceive('isDeprecated')->andReturn(true);
        $fileDescriptor->shouldReceive('getAllErrors->getAll')->andReturn($errors);
        $fileDescriptor->shouldReceive('getMarkers->count')->andReturn($markerCount);
        return $fileDescriptor;
    }

    /**
     * @return m\MockInterface
     */
    private function givenATransformer()
    {
        $transformer = m::mock('phpDocumentor\Transformer\Transformation');
        $transformer->shouldReceive('getTransformer->getTarget')->andReturn(vfsStream::url('StatisticsTest'));
        $transformer->shouldReceive('getArtifact')->andReturn('artifact.xml');
        return $transformer;
    }

    /**
     * @return m\MockInterface
     */
    private function givenAnError()
    {
        $error = m::mock('phpDocumentor\Descriptor\Validator\Error');
        return $error;
    }

    private function thenTheXmlReportShouldContain(
        $date,
        $numberOfFiles,
        $numberOfDeprecated,
        $numberOfErrors,
        $numberOfMarkers
    ) {
        $version = file_get_contents(__DIR__ . '/../../../../../../../VERSION');

        $expectedXml = new \DOMDocument;
        $expectedXml->loadXML(
            '<?xml version="1.0"?>
<phpdoc-stats version="' . $version . '">
  <stat date="'.$date.'">
    <counters>
        <files>'.$numberOfFiles.'</files>
        <deprecated>'.$numberOfDeprecated.'</deprecated>
        <errors>'.$numberOfErrors.'</errors>
        <markers>'.$numberOfMarkers.'</markers>
    </counters>
</stat>
</phpdoc-stats>'
        );

        $actualXml = new \DOMDocument;
        $actualXml->load(vfsStream::url('StatisticsTest/artifact.xml'));

        $actualTime   = $this->getGeneratedDateTime($actualXml);
        $expectedTime = $this->getGeneratedDateTime($expectedXml);
        $diff = $actualTime->diff($expectedTime, true);

        // overwrite to prevent timing issues, otherwise the test might fail due to a second difference
        $this->setGeneratedDateTime($actualXml, $expectedTime);

        // time could have switch a second in between; verify within a range of 2 seconds
        $this->assertLessThanOrEqual(2, $diff->s);
        $this->assertEqualXMLStructure($expectedXml->firstChild, $actualXml->firstChild, true);
        $this->assertSame($expectedXml->saveXML(), $actualXml->saveXML());
    }

    /**
     * @param $actualXml
     *
     * @return \DateTime
     */
    private function getGeneratedDateTime($actualXml)
    {
        return new \DateTime(
            $actualXml->getElementsByTagName('stat')->item(0)->attributes->getNamedItem('date')->nodeValue
        );
    }

    /**
     * @param $actualXml
     *
     * @return \DateTime
     */
    private function setGeneratedDateTime($actualXml, \DateTime $dateTime)
    {
        $actualXml->getElementsByTagName('stat')->item(0)->attributes->getNamedItem('date')->nodeValue
            = $dateTime->format(DATE_ATOM);
    }
}
