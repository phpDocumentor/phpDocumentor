<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use Monolog\Logger;
use phpDocumentor\Application;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Plugin\Core\Exception;
use phpDocumentor\Transformer\Event\PreXslWriterEvent;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformation as TransformationObject;
use phpDocumentor\Transformer\Writer\Exception\RequirementMissing;
use phpDocumentor\Transformer\Writer\WriterAbstract;

/**
 * XSL transformation writer; generates static HTML out of the structure and XSL templates.
 */
class Xsl extends WriterAbstract
{
    /** @var \Monolog\Logger $logger */
    protected $logger;

    protected $xsl_variables = array();

    /**
     * Initialize this writer with the logger so that it can output logs.
     *
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Checks whether XSL handling is enabled with PHP as that is not enabled by default.
     *
     * To enable XSL handling you need either the xsl extension or the xslcache extension installed.
     *
     * @throws RequirementMissing if neither xsl extensions are installed.
     *
     * @return void
     */
    public function checkRequirements()
    {
        if (!class_exists('XSLTProcessor') && (!extension_loaded('xslcache'))) {
            throw new RequirementMissing(
                'The XSL writer was unable to find your XSLTProcessor; '
                . 'please check if you have installed the PHP XSL extension or XSLCache extension'
            );
        }
    }

    /**
     * This method combines the structure.xml and the given target template
     * and creates a static html page at the artifact location.
     *
     * @param ProjectDescriptor $project        Document containing the structure.
     * @param Transformation    $transformation Transformation to execute.
     *
     * @throws \RuntimeException if the structure.xml file could not be found.
     * @throws Exception        if the structure.xml file's documentRoot could not be read because of encoding issues
     *    or because it was absent.
     *
     * @return void
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation)
    {
        $artifact = $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();

        $structureFilename = $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . 'structure.xml';
        if (!is_readable($structureFilename)) {
            throw new \RuntimeException(
                'Structure.xml file was not found in the target directory, is the XML writer missing from the '
                . 'template definition?'
            );
        }

        // load the structure file (ast)
        $structure = new \DOMDocument('1.0', 'utf-8');
        libxml_use_internal_errors(true);
        $structure->load($structureFilename);

        $proc = $this->getXslProcessor($transformation);

        if (empty($structure->documentElement)) {
            $message = 'Specified DOMDocument lacks documentElement, cannot transform.';
            if (libxml_get_last_error()) {
                $message .= PHP_EOL . 'Apparently an error occurred with reading the structure.xml file, the reported '
                . 'error was "' . trim(libxml_get_last_error()->message) . '" on line ' . libxml_get_last_error()->line;
            }
            throw new Exception($message);
        }

        $proc->setParameter('', 'title', $structure->documentElement->getAttribute('title'));
        $proc->setParameter('', 'root', str_repeat('../', substr_count($transformation->getArtifact(), '/')));
        $proc->setParameter('', 'search_template', $transformation->getParameter('search', 'none'));
        $proc->setParameter('', 'version', Application::$VERSION);
        $proc->setParameter('', 'generated_datetime', date('r'));

        // check parameters for variables and add them when found
        $this->setProcessorParameters($transformation, $proc);

        // if a query is given, then apply a transformation to the artifact
        // location by replacing ($<var>} with the sluggified node-value of the
        // search result
        if ($transformation->getQuery() !== '') {
            $xpath = new \DOMXPath($structure);

            /** @var \DOMNodeList $qry */
            $qry = $xpath->query($transformation->getQuery());
            $count = $qry->length;
            foreach ($qry as $key => $element) {
                Dispatcher::getInstance()->dispatch(
                    'transformer.writer.xsl.pre',
                    PreXslWriterEvent
                    ::createInstance($this)->setElement($element)
                    ->setProgress(array($key+1, $count))
                );

                $proc->setParameter('', $element->nodeName, $element->nodeValue);
                $file_name = $transformation->getTransformer()->generateFilename(
                    $element->nodeValue
                );

                $filename = str_replace('{$' . $element->nodeName . '}', $file_name, $artifact);

                if (!file_exists(dirname($filename))) {
                    mkdir(dirname($filename), 0755, true);
                }
                $proc->transformToURI($structure, $this->getXsltUriFromFilename($filename));
            }
        } else {
            if (substr($transformation->getArtifact(), 0, 1) == '$') {
                // not a file, it must become a variable!
                $variable_name = substr($transformation->getArtifact(), 1);
                $this->xsl_variables[$variable_name]
                    = $proc->transformToXml($structure);
            } else {
                if (!file_exists(dirname($artifact))) {
                    mkdir(dirname($artifact), 0755, true);
                }
                $proc->transformToURI($structure, $this->getXsltUriFromFilename($artifact));
            }
        }
    }

    /**
     * Takes the filename and converts it into a correct URI for XSLTProcessor.
     *
     * @param string $filename
     *
     * @return string
     */
    protected function getXsltUriFromFilename($filename)
    {
        // Windows requires an additional / after the scheme.
        // If not provided then errno 22 (I/O Error: Invalid Argument) will be
        // raised. Thanks to @FnTmLV for finding the cause.
        // See issue #284 for more information
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
            $filename = '/' . $filename;
        }

        return 'file://' . $filename;
    }

    /**
     * Sets the parameters of the XSLT processor.
     *
     * @param TransformationObject $transformation Transformation.
     * @param \XSLTProcessor       $proc           XSLTProcessor.
     *
     * @return void
     */
    public function setProcessorParameters(
        TransformationObject $transformation,
        \XSLTProcessor $proc
    ) {
        foreach ($this->xsl_variables as $key => $variable) {
            // XSL does not allow both single and double quotes in a string
            if ((strpos($variable, '"') !== false)
                && ((strpos($variable, "'") !== false))
            ) {
                // TODO: inject the logger and use it here
                //$this->log(
                //    'XSLT does not allow both double and single quotes in '
                //    . 'a variable; transforming single quotes to a character '
                //    . 'encoded version in variable: ' . $key,
                //    LogLevel::WARNING
                //);
                $variable = str_replace("'", "&#39;", $variable);
            }

            $proc->setParameter('', $key, $variable);
        }

        // add / overwrite the parameters with those defined in the
        // transformation entry
        $parameters = $transformation->getParameters();
        if (isset($parameters['variables'])) {
            /** @var \DOMElement $variable */
            foreach ($parameters['variables'] as $key => $value) {
                $proc->setParameter('', $key, $value);
            }
        }
    }

    /**
     *
     *
     * @param Transformation $transformation
     *
     * @return \XSLTCache|\XSLTProcessor
     */
    protected function getXslProcessor(Transformation $transformation)
    {
        $xslTemplatePath = $transformation->getSourceAsPath();
        $this->logger->debug('Loading XSL template: ' . $xslTemplatePath);
        if (!file_exists($xslTemplatePath)) {
            throw new Exception('Unable to find XSL template "' . $xslTemplatePath . '"');
        }

        if (extension_loaded('xslcache')) {
            $proc = new \XSLTCache();
            $proc->importStyleSheet($xslTemplatePath, true);

            return $proc;
        } else {
            $xsl = new \DOMDocument();
            $xsl->load($xslTemplatePath);

            $proc = new \XSLTProcessor();
            $proc->importStyleSheet($xsl);

            return $proc;
        }
    }
}
