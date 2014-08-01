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

namespace phpDocumentor\Plugin\Scrybe\Converter\RestructuredText;

use Monolog\Logger;
use phpDocumentor\Fileset\File;
use phpDocumentor\Plugin\Scrybe\Converter\ConverterInterface;

/**
 * This is a customized RestructuredText document to register Scrybe-specific directives, roles and options.
 *
 * The following directives are introduced using this class:
 *
 * - toctree, a directive used to insert table of contents into documents.
 * - image, an overridden version of `image` that collects the assets.
 * - figure, an overridden version of the `figure` that collects the assets.
 *
 * The following roles are introduced in this class:
 *
 * - doc, a reference to an external piece of documentation.
 *
 * @property \ezcDocumentRstOptions $options
 */
class Document extends \ezcDocumentRst
{
    /**
     * Fileset containing the project root and list of files in this run.
     *
     * @var File
     */
    protected $file;

    /**
     * Converter used to retrieve global assets from.
     *
     * The converter contains global assets, such as the Table of Contents, that can be used in directives and roles.
     *
     * @var ConverterInterface
     */
    protected $converter;

    /**
     * Sets the Scrybe-specific options, registers the roles and directives and loads the file.
     *
     * @param ConverterInterface $converter
     * @param File $file
     */
    public function __construct(ConverterInterface $converter, File $file)
    {
        parent::__construct();

        $this->options->xhtmlVisitor = 'phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Visitors\Creator';
        $this->options->errorReporting = E_PARSE | E_ERROR;

        $this->registerDirective(
            'code-block',
            'phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Directives\CodeBlock'
        );
        $this->registerDirective(
            'toctree',
            'phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Directives\Toctree'
        );
        $this->registerDirective(
            'image',
            'phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Directives\Image'
        );
        $this->registerDirective(
            'figure',
            'phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Directives\Figure'
        );
        $this->registerRole(
            'doc',
            'phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Roles\Doc'
        );

        $this->file      = $file;
        $this->converter = $converter;

        $this->loadString($file->fread());
    }

    /**
     * Returns the converter responsible for converting this object.
     *
     * @return ConverterInterface
     */
    public function getConverter()
    {
        return $this->converter;
    }

    /**
     * Returns the file associated with this document.
     *
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sends the errors of the given Rst document to the logger as a block.
     *
     * If a fatal error occurred then this can be passed as the $fatal argument and is shown as such.
     *
     * @param \Exception|null $fatal
     * @param Logger $logger
     *
     * @return void
     */
    public function logStats($fatal, Logger $logger)
    {
        if (!$this->getErrors() && !$fatal) {
            return;
        }

        /** @var \Exception $error */
        foreach ($this->getErrors() as $error) {
            $logger->warning('  ' . $error->getMessage());
        }
        if ($fatal) {
            $logger->error('  ' . $fatal->getMessage());
        }
    }
}
