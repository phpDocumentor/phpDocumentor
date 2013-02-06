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
        $artifact = $transformation->getTransformer()->getTarget()
        . DIRECTORY_SEPARATOR . $transformation->getArtifact();

        $list = array();

        /** @var FileDescriptor $file */
        foreach ($project->getFiles() as $file) {
            array_merge($list, $file->getErrors()->getArrayCopy());
        }

        $document = new \DOMDocument();
        $document->formatOutput = true;
        $report = $document->createElement('checkstyle');
        $report->setAttribute('version', '1.3.0');
        $document->appendChild($report);

        foreach ($list as $node) {

            $file = $document->createElement('file');
//            $file->setAttribute('name', $node->parentNode->getAttribute('path'));
            $report->appendChild($file);

            foreach ($node->childNodes as $error) {
                // FIXME
                $item = $document->createElement('error');
                $item->setAttribute('line', '');
                $item->setAttribute('severity', '');
                $item->setAttribute('message', '');
                $item->setAttribute('source', 'phpDocumentor.phpDocumentor.phpDocumentor');
                $file->appendChild($item);
            }
        }

        $this->saveCheckstyleReport($artifact, $document);
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
