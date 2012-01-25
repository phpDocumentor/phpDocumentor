<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

/**
 * A PDF writer which uses wkhtmltopdf to convert a single HTML file to PDF.
 *
 * It is advised to have your own version of wkhtmltopdf installed on the
 * machine where phpDocumentor runs. wkhtmltopdf relies on the presence of xserver
 * (not necessarily running; in case of linux) to invoke webkit to generate the
 * PDF.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 * @link       http://code.google.com/p/wkhtmltopdf/
 * @link       http://blog.structuralartistry.com/post/2327213260/
 *             installing-wkhtmltopdf-on-ubuntu-server
 */
class phpDocumentor_Plugin_Core_Transformer_Writer_Pdf
    extends phpDocumentor_Transformer_Writer_Abstract
{
    /**
     * Calls the wkhtmltopdf executable to generate a PDF.
     *
     * @param DOMDocument                        $structure      Structure source
     *     use as basis for the transformation.
     * @param phpDocumentor_Transformer_Transformation $transformation Transformation
     *     that supplies the meta-data for this writer.
     *
     * @return void
     */
    public function transform(
        DOMDocument $structure, phpDocumentor_Transformer_Transformation $transformation
    ) {
        $artifact = $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        $transformation->setArtifact($artifact);

        $source = substr($transformation->getSource(), 0, 1) != DIRECTORY_SEPARATOR
                ? $transformation->getTransformer()->getTarget()
                      . DIRECTORY_SEPARATOR . $transformation->getSource()
                : $transformation->getSource();
        $transformation->setSource($source);

        $options = '';
        if ($transformation->getParameter('toc', 'false') == 'true') {
            $options = ' toc ';
        }

        // TODO: add parameter to provide a cover HTML
        // TODO: add a parameter to provide a header HTML
        // TODO: add a parameter to provide a footer HTML

        // first try if there is a wkhtmltopdf in the global path, this helps
        // windows users
        exec(
            'wkhtmltopdf ' . $options . ' ' . $transformation->getSource()
            . ' ' . $transformation->getArtifact() . ' 2>&1',
            $output,
            $error
        );

        $output = implode(PHP_EOL, $output);

        // this notice is linux specific; if it is found no global wkhtmltopdf
        // was installed; try the one which is included with phpDocumentor
        if (strpos($output, 'wkhtmltopdf: not found') !== false) {
            // TODO: replace the below with a decent way to find the executable
            exec(
                dirname(__FILE__) . '/../../../src/wkhtmltopdf/wkhtmltopdf-i386 '
                . $options . ' ' . $transformation->getSource() . ' '
                . $transformation->getArtifact() . ' 2>&1',
                $output,
                $error
            );

            $output = implode(PHP_EOL, $output) . PHP_EOL;
        }

        // log message and output
        $this->log(
            'Generating PDF file ' . $transformation->getArtifact()
            . ' from ' . $transformation->getSource()
        );
        $this->log(
            $output,
            $error == 0 ? phpDocumentor_Core_Log::INFO : phpDocumentor_Core_Log::CRIT
        );

        // CRASH!
        if ($error != 0) {
            throw new phpDocumentor_Transformer_Exception(
                'Conversion to PDF failed, see output for details'
            );
        }
    }

}