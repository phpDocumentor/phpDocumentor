<?php

/**
 * A PDF writer which uses wkhtmltopdf to convert a single HTML file to PDF.
 *
 * It is advised to have your own version of wkhtmltopdf installed on the machine where DocBlox runs.
 * wkhtmltopdf relies on the presence of xserver (not necessarily running; in case of linux) to invoke webkit to
 * generate the PDF.
 *
 * @package    DocBlox
 * @subpackage Writer
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 *
 * @see        http://code.google.com/p/wkhtmltopdf/
 * @see        http://blog.structuralartistry.com/post/2327213260/installing-wkhtmltopdf-on-ubuntu-server
 */
class DocBlox_Writer_Pdf extends DocBlox_Writer_Abstract
{
  /**
   * Calls the wkhtmltopdf executable to generate a PDF.
   *
   * @param DOMDocument            $structure
   * @param DocBlox_Transformation $transformation
   *
   * @return void
   */
  public function transform(DOMDocument $structure, DocBlox_Transformation $transformation)
  {
    $artifact = $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getArtifact();
    $transformation->setArtifact($artifact);

    $source = substr($transformation->getSource(), 0, 1) != DIRECTORY_SEPARATOR
      ? $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getSource()
      : $transformation->getSource();
    $transformation->setSource($source);

    $options = '';
    if ($transformation->getParameter('toc', 'false') == 'true')
    {
      $options = ' toc ';
    }

    // TODO: add parameter to provide a cover HTML
    // TODO: add a parameter to provide a header HTML
    // TODO: add a parameter to provide a footer HTML

    // first try if there is a wkhtmltopdf in the global path, this helps windows users
    exec('wkhtmltopdf ' . $options . ' ' . $transformation->getSource() . ' ' . $transformation->getArtifact() . ' 2>&1', $output, $error);
    $output = implode(PHP_EOL, $output);

    // this notice is linux specific; if it is found no global wkhtmltopdf was installed; try the one which is included
    // with docblox
    if (strpos($output, 'wkhtmltopdf: not found') !== false)
    {
      exec($this->getConfig()->paths->application . '/lib/wkhtmltopdf/wkhtmltopdf-i386 ' . $options . ' '
        . $transformation->getSource() . ' ' . $transformation->getArtifact() . ' 2>&1', $output, $error);
      $output = implode(PHP_EOL, $output).PHP_EOL;
    }

    // log message and output
    $this->log('Generating PDF file ' . $transformation->getArtifact() . ' from ' . $transformation->getSource());
    $this->log($output, $error == 0 ? DocBlox_Log::INFO : DocBlox_Log::CRIT);

    // CRASH!
    if ($error != 0)
    {
      throw new Exception('Conversion to PDF failed, see output for details');
    }
  }

}