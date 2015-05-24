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

namespace phpDocumentor\Plugin\Core;

use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Plugin\Graphs\ServiceProvider as GraphServiceProvider;
use phpDocumentor\Plugin\Twig\ServiceProvider as TwigServiceProvider;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Plugin\Core\Transformer\Writer;
use phpDocumentor\Transformer\Writer\Collection;

/**
 * Register all services and subservices necessary to get phpDocumentor up and running.
 *
 * This provider exposes no services of its own but populates the Writer Collection with the basic writers for
 * phpDocumentor and, for backwards compatibility, registers the service providers for Graphs, Twig and PDF to
 * the container.
 */
final class ServiceProvider
{
    /** @var Collection */
    private $writerCollection;

    /** @var GraphServiceProvider */
    private $graphProvider;

    /** @var TwigServiceProvider */
    private $twigProvider;

    /** @var Writer\FileIo */
    private $fileIoWriter;

    /** @var Writer\Checkstyle */
    private $checkstyleWriter;

    /** @var Writer\Sourcecode */
    private $sourcecodeWriter;

    /** @var Writer\Statistics */
    private $statisticsWriter;

    /** @var Writer\Xml */
    private $xmlWriter;

    /** @var Writer\Xsl */
    private $xslWriter;

    /** @var Writer\Jsonp */
    private $jsonpWriter;

    /** @var Queue */
    private $queue;

    /** @var Analyzer */
    private $analyzer;

    public function __construct(
        Collection           $writerCollection,
        GraphServiceProvider $graphProvider,
        TwigServiceProvider  $twigProvider,
        Writer\FileIo        $fileIoWriter,
        Writer\Checkstyle    $checkstyleWriter,
        Writer\Sourcecode    $sourcecodeWriter,
        Writer\Statistics    $statisticsWriter,
        Writer\Xml           $xmlWriter,
        Writer\Xsl           $xslWriter,
        Writer\Jsonp         $jsonpWriter,
        Queue                $queue,
        Analyzer             $analyzer
    ) {
        $this->writerCollection = $writerCollection;
        $this->graphProvider    = $graphProvider;
        $this->twigProvider     = $twigProvider;
        $this->fileIoWriter     = $fileIoWriter;
        $this->checkstyleWriter = $checkstyleWriter;
        $this->sourcecodeWriter = $sourcecodeWriter;
        $this->statisticsWriter = $statisticsWriter;
        $this->xmlWriter        = $xmlWriter;
        $this->xslWriter        = $xslWriter;
        $this->jsonpWriter      = $jsonpWriter;
        $this->queue            = $queue;
        $this->analyzer         = $analyzer;
    }

    /**
     * Registers services on the given app.
     *
     * @return void
     */
    public function __invoke()
    {
        $this->writerCollection['FileIo']     = $this->fileIoWriter;
        $this->writerCollection['checkstyle'] = $this->checkstyleWriter;
        $this->writerCollection['sourcecode'] = $this->sourcecodeWriter;
        $this->writerCollection['statistics'] = $this->statisticsWriter;
        $this->writerCollection['xml']        = $this->xmlWriter;
        $this->writerCollection['xsl']        = $this->xslWriter;
        $this->writerCollection['jsonp']      = $this->jsonpWriter;

        Xslt\Extension::$routers = $this->queue;
        Xslt\Extension::$analyzer = $this->analyzer;

        call_user_func($this->twigProvider);
        call_user_func($this->graphProvider);
    }
}
