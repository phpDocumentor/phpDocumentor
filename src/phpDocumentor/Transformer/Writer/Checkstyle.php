<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

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
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation)
    {
        $artifact = $this->getDestinationPath($transformation);

        $this->checkForSpacesInPath($artifact);

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

            /** @var Error $error */
            foreach ($fileDescriptor->getAllErrors()->getAll() as $error) {
                $item = $document->createElement('error');
                $item->setAttribute('line', (string) $error->getLine());
                $item->setAttribute('severity', $error->getSeverity());
                $item->setAttribute('message', vsprintf($error->getCode(), $error->getContext()));
                $item->setAttribute('source', 'phpDocumentor.file.' . $error->getCode());
                $file->appendChild($item);
            }
        }

        $this->saveCheckstyleReport($artifact, $document);
    }

    /**
     * Retrieves the destination location for this artifact.
     *
     * @return string
     */
    protected function getDestinationPath(Transformation $transformation)
    {
        return $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
    }

    /**
     * Save the checkstyle report to the artifact
     *
     * @param string       $artifact Target name for the report
     * @param \DOMDocument $document The actual xml document being saved
     */
    protected function saveCheckstyleReport($artifact, \DOMDocument $document)
    {
        file_put_contents($artifact, $document->saveXML());
    }
}
