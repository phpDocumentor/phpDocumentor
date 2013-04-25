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

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\WriterAbstract;

/**
 * Checkstyle transformation writer; generates checkstyle report
 */
class Checkstyle extends WriterAbstract
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

        $document = new \DOMDocument();
        $document->formatOutput = true;
        $report = $document->createElement('checkstyle');
        $report->setAttribute('version', '1.3.0');
        $document->appendChild($report);

        /** @var FileDescriptor $fileDescriptor */
        foreach ($project->getFiles()->getAll() as $fileDescriptor) {
            $file = $document->createElement('file');
            $file->setAttribute('name', $fileDescriptor->getPath());
            $report->appendChild($file);

            foreach ($fileDescriptor->getErrors()->getAll() as $error) {
                $item = $document->createElement('error');
                $item->setAttribute('line', $error['line']);
                $item->setAttribute('severity', $error['type']);
                $item->setAttribute('message', $error['message']);
                $item->setAttribute('source', 'phpDocumentor.file.'.$error['code']);
                $file->appendChild($item);
            }
        }

        $this->saveCheckstyleReport($artifact, $document);
    }

    /**
     * Retrieves the destination location for this artifact.
     *
     * @param \phpDocumentor\Transformer\Transformation $transformation
     *
     * @return string
     */
    protected function getDestinationPath(Transformation $transformation)
    {
        $artifact = $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        return $artifact;
    }

    /**
     * Save the checkstyle report to the artifact
     *
     * @param string      $artifact Target name for the report
     * @param \DOMDocument $document The actual xml document being saved
     *
     * @return void
     */
    protected function saveCheckstyleReport($artifact, \DOMDocument $document)
    {
        file_put_contents($artifact, $document->saveXML());
    }
}
