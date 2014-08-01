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

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Translatable;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use phpDocumentor\Translator\Translator;

/**
 * Checkstyle transformation writer; generates checkstyle report
 */
class Checkstyle extends WriterAbstract implements Translatable
{
    /** @var Translator $translator */
    protected $translator;

    /**
     * Returns an instance of the object responsible for translating content.
     *
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Sets a new object capable of translating strings on this writer.
     *
     * @param Translator $translator
     *
     * @return void
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }

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
                $item->setAttribute('line', $error->getLine());
                $item->setAttribute('severity', $error->getSeverity());
                $item->setAttribute(
                    'message',
                    vsprintf($this->getTranslator()->translate($error->getCode()), $error->getContext())
                );
                $item->setAttribute('source', 'phpDocumentor.file.'.$error->getCode());
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
     * @param string       $artifact Target name for the report
     * @param \DOMDocument $document The actual xml document being saved
     *
     * @return void
     */
    protected function saveCheckstyleReport($artifact, \DOMDocument $document)
    {
        file_put_contents($artifact, $document->saveXML());
    }
}
