<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Application;

/**
 * Statistics transformation writer; generates statistic report as XML.
 *
 * Generated XML structure:
 * ```
 *  <?xml version="1.0"?>
 *  <phpdoc-stats version="2.4.0">
 *    <stat date="2014-06-02T19:26:15+02:00">
 *      <counters>
 *        <deprecated>100</deprecated>
 *        <errors>377</errors>
 *        <markers>2</markers>
 *      </counters>
 *    </stat>
 *  </phpdoc-stats>
 * ```
 *
 * @author Siad Ardroumli <siad.ardroumli@gmail.com>
 */
class Statistics extends Checkstyle
{
    /**
     * This method generates the checkstyle.xml report
     *
     * @param ProjectDescriptor $project        Document containing the structure.
     * @param Transformation    $transformation Transformation to execute.
     *
     * @return void
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation)
    {
        $artifact = $this->getDestinationPath($transformation);

        $now = new \DateTime('now');
        $date = $now->format(DATE_ATOM);

        $document = new \DOMDocument();
        $document->formatOutput = true;
        $document->preserveWhiteSpace = false;

        if (is_file($artifact)) {
            $document->load($artifact);
        } else {
            $document = $this->appendPhpdocStatsElement($document);
        }

        $document = $this->appendStatElement($document, $project, $date);

        $this->saveCheckstyleReport($artifact, $document);
    }

    /**
     * Append phpdoc-stats element to the document.
     *
     * @param \DOMDocument $document
     *
     * @return \DOMDocument
     */
    protected function appendPhpdocStatsElement(\DOMDocument $document)
    {
        $stats = $document->createElement('phpdoc-stats');
        $stats->setAttribute('version', Application::$VERSION);
        $document->appendChild($stats);

        return $document;
    }

    /**
     * Appends a stat fragment.
     *
     * @param \DOMDocument $document
     * @param ProjectDescriptor $project
     * @param string $date
     *
     * @return \DOMDocument
     */
    protected function appendStatElement(\DOMDocument $document, ProjectDescriptor $project, $date)
    {
        $stat = $document->createDocumentFragment();
        $stat->appendXML(
<<<STAT
<stat date="$date">
    <counters>
        <files>{$this->getFilesCounter($project)}</files>
        <deprecated>{$this->getDeprecatedCounter($project)}</deprecated>
        <errors>{$this->getErrorCounter($project)}</errors>
        <markers>{$this->getMarkerCounter($project)}</markers>
    </counters>
</stat>
STAT
        );
        $document->documentElement->appendChild($stat);

        return $document;
    }

    /**
     * Get number of files.
     *
     * @param ProjectDescriptor $project
     *
     * @return int
     */
    protected function getFilesCounter(ProjectDescriptor $project)
    {
        return $project->getFiles()->count();
    }

    /**
     * Get number of deprecated elements.
     *
     * @param ProjectDescriptor $project
     *
     * @return int
     */
    protected function getDeprecatedCounter(ProjectDescriptor $project)
    {
        $deprecatedCounter = 0;

        /** @var DescriptorAbstract $element */
        foreach ($project->getIndexes()->get('elements') as $element) {
            if ($element->isDeprecated()) {
                $deprecatedCounter += 1;
            }
        }

        return $deprecatedCounter;
    }

    /**
     * Get number of errors.
     *
     * @param ProjectDescriptor $project
     *
     * @return int
     */
    protected function getErrorCounter(ProjectDescriptor $project)
    {
        $errorCounter = 0;

        /* @var FileDescriptor $fileDescriptor */
        foreach ($project->getFiles()->getAll() as $fileDescriptor) {
            $errorCounter += count($fileDescriptor->getAllErrors()->getAll());
        }

        return $errorCounter;
    }

    /**
     * Get number of markers.
     *
     * @param ProjectDescriptor $project
     *
     * @return int
     */
    protected function getMarkerCounter(ProjectDescriptor $project)
    {
        $markerCounter = 0;

        /* @var $fileDescriptor FileDescriptor */
        foreach ($project->getFiles()->getAll() as $fileDescriptor) {
            $markerCounter += $fileDescriptor->getMarkers()->count();
        }

        return $markerCounter;
    }
}
