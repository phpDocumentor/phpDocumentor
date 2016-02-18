<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Renderer\Template\Action;

use phpDocumentor\DomainModel\Renderer\Template\Action;

/**
 * Checkstyle writer; generates checkstyle report
 */
class CheckstyleHandler
{
    /**
     * @var Analyzer
     */
    private $analyzer;

    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * This method generates the checkstyle.xml report
     *
     * @param Checkstyle $action
     *
     * @return void
     */
    public function __invoke(Action $action)
    {
        $project = $this->analyzer->getProjectDescriptor();

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
                $item->setAttribute('message', vsprintf($error->getCode(), $error->getContext()));
                $item->setAttribute('source', 'phpDocumentor.file.'.$error->getCode());
                $file->appendChild($item);
            }
        }

        $fs = $action->getRenderContext()->getFilesystem();
        $fs->put(
            $action->getRenderContext()->getDestination() . '/' . ltrim($action->getDestination(), '\\/'),
            $document->saveXML()
        );
    }
}
