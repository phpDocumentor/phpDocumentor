<?php
/**
 * Checkstyle Transformer File
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Ben Selby <benmatselby@gmail.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

/**
 * Checkstyle transformation writer; generates checkstyle report
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Ben Selby <benmatselby@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Checkstyle extends \phpDocumentor\Transformer\Writer\WriterAbstract
{
    /**
     * This method generates the checkstyle.xml report
     *
     * @param \DOMDocument                              $structure      XML source.
     * @param \phpDocumentor\Transformer\Transformation $transformation Transformation.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function transform(\DOMDocument $structure,
        \phpDocumentor\Transformer\Transformation $transformation
    ) {
        $artifact = $transformation->getTransformer()->getTarget()
        . DIRECTORY_SEPARATOR . $transformation->getArtifact();

        $list = $structure->getElementsByTagName('parse_markers');

        $document = new \DOMDocument();
        $document->formatOutput = true;
        $report = $document->createElement('checkstyle');
        $report->setAttribute('version', '1.3.0');
        $document->appendChild($report);

        foreach ($list as $node) {

            $file = $document->createElement('file');
            $file->setAttribute('name', $node->parentNode->getAttribute('path'));
            $report->appendChild($file);

            foreach ($node->childNodes as $error) {

                if ((string)$error->nodeName != '#text') {
                    $item = $document->createElement('error');
                    $item->setAttribute('line', $error->getAttribute('line'));
                    $item->setAttribute('severity', $error->nodeName);
                    $item->setAttribute('message', $error->textContent);
                    $item->setAttribute('source', 'phpDocumentor.phpDocumentor.phpDocumentor');
                    $file->appendChild($item);
                }
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