<?php

/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Pascal de Vink <pascal.de.vink@gmail.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use Mockery as m;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * Test class for \phpDocumentor\Transformer\Writer\Statistics.
 */
class StatisticsTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var Statistics
     */
    protected $statistics;

    /** @var vfsStreamDirectory */
    private $fs;

    /**
     * Sets up the test suite
     */
    protected function setUp()
    {
        $this->statistics = $this->getMockBuilder('phpDocumentor\Transformer\Writer\Statistics')
            ->setMethods(['getDestinationPath'])
            ->getMock();
        $this->statistics->method('getDestinationPath')
            ->willReturn(vfsStream::url('StatisticsTest/artifact.xml'));

        $this->fs = vfsStream::setup('StatisticsTest');
    }

    public function testTransformWithStartingArtifactAsString()
    {
        $markerCount = 1;
        $transformer = $this->givenATransformer();
        $error = $this->givenAnError();
        $fileDescriptor = $this->givenAFileDescriptor([$error], $markerCount);
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
        $version = trim(file_get_contents(__DIR__ . '/../../../../../VERSION'));
        $statsXml = '<?xml version="1.0"?><phpdoc-stats version="' . $version . '"></phpdoc-stats>';
        vfsStream::create(['artifact.xml' => $statsXml]);

        $markerCount = 12;
        $transformer = $this->givenATransformer();
        $error = $this->givenAnError();
        $fileDescriptor = $this->givenAFileDescriptor([$error, $error], $markerCount);
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
     * @return m\MockInterface
     */
    private function givenAProjectDescriptor(m\MockInterface $fileDescriptor)
    {
        $projectDescriptor = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $projectDescriptor->shouldReceive('getFiles->getAll')->andReturn([$fileDescriptor]);
        $projectDescriptor->shouldReceive('getFiles->count')->andReturn(1);
        $projectDescriptor->shouldReceive('getIndexes->get')->andReturn([$fileDescriptor]);
        return $projectDescriptor;
    }

    /**
     * @param int $markerCount
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
        return m::mock('phpDocumentor\Descriptor\Validator\Error');
    }

    private function thenTheXmlReportShouldContain(
        $date,
        $numberOfFiles,
        $numberOfDeprecated,
        $numberOfErrors,
        $numberOfMarkers
    ) {
        $version = trim(file_get_contents(__DIR__ . '/../../../../../VERSION'));

        $expectedXml = new \DOMDocument();
        $expectedXml->loadXML(
            '<?xml version="1.0"?>
<phpdoc-stats version="' . $version . '">
  <stat date="' . $date . '">
    <counters>
        <files>' . $numberOfFiles . '</files>
        <deprecated>' . $numberOfDeprecated . '</deprecated>
        <errors>' . $numberOfErrors . '</errors>
        <markers>' . $numberOfMarkers . '</markers>
    </counters>
</stat>
</phpdoc-stats>'
        );

        $actualXml = new \DOMDocument();
        $actualXml->load(vfsStream::url('StatisticsTest' . DIRECTORY_SEPARATOR . 'artifact.xml'));

        $actualTime = $this->getGeneratedDateTime($actualXml);
        $expectedTime = $this->getGeneratedDateTime($expectedXml);
        $diff = $actualTime->diff($expectedTime, true);

        // overwrite to prevent timing issues, otherwise the test might fail due to a second difference
        $this->setGeneratedDateTime($actualXml, $expectedTime);

        // time could have switch a second in between; verify within a range of 2 seconds
        $this->assertLessThanOrEqual(2, $diff->s);
        $this->assertEqualXMLStructure($expectedXml->firstChild, $actualXml->firstChild, true);
        $this->assertSame($expectedXml->saveXML(), $actualXml->saveXML());
    }

    private function getGeneratedDateTime(\DOMDocument $actualXml): \DateTime
    {
        return new \DateTime(
            $actualXml->getElementsByTagName('stat')->item(0)->attributes->getNamedItem('date')->nodeValue
        );
    }

    private function setGeneratedDateTime(\DOMDocument $actualXml, \DateTime $dateTime)
    {
        $actualXml->getElementsByTagName('stat')->item(0)->attributes->getNamedItem('date')->nodeValue
            = $dateTime->format(DATE_ATOM);
    }
}
