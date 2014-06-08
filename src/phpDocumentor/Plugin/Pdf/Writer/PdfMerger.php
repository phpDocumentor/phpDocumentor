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

namespace phpDocumentor\Plugin\Pdf\Writer;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use ZendPdf\PdfDocument;

/**
 * Writer respondable to merge several pdf documents.
 *
 * Usage in template.xml would be:
 * `<transformation writer="PdfMerger" source="markers.pdf,errors.pdf,deprecated.pdf" artifact="docu.pdf"/>`
 *
 * @author Siad Ardroumli <siad.ardroumli@gmail.com>
 */
class PdfMerger extends WriterAbstract
{
    /**
     * This method combines pdf documents at the given target target
     * and creates a static pdf file at the artifact location.
     *
     * @param ProjectDescriptor $project        Document containing the structure.
     * @param Transformation    $transformation Transformation to execute.
     *
     * @return void
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation)
    {
        $source = $transformation->getSource();
        $target = $transformation->getTransformer()->getTarget();

        $documents = explode(',', $source);

        $pdfMerged = new PdfDocument();

        foreach ($documents as $document) {
            $pdfDocument = PdfDocument::load($target . DIRECTORY_SEPARATOR . $document);

            foreach ($pdfDocument->pages as $page) {
                $clonedPage = clone $page;
                $pdfMerged->pages[] = $clonedPage;
            }
        }

        $pdfMerged->save(
            $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR
            . $transformation->getArtifact()
        );
    }
}
