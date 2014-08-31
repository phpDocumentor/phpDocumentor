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
use phpDocumentor\Transformer\Router\ForFileProxy;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformation as TransformationObject;
use phpDocumentor\Transformer\Writer\Exception\RequirementMissing;
use phpDocumentor\Transformer\Writer\Routable;
use phpDocumentor\Transformer\Writer\WriterAbstract;

/**
 * XSL transformation writer; generates static HTML out of the structure and XSL templates.
 */
class Xsl extends WriterAbstract implements Routable
{
    /** @var \Monolog\Logger $logger */
    protected $logger;

    /** @var string[] */
    protected $xsl_variables = array();

    /** @var Queue */
    private $routers;

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
     * Sets the routers that can be used to determine the path of links.
     *
     * @param Queue $routers
     *
     * @return void
     */
    public function setRouters(Queue $routers)
    {
        $this->routers = $routers;
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
        $structure = $this->loadAst($this->getAstPath($transformation));

        $proc = $this->getXslProcessor($transformation);
        $proc->registerPHPFunctions();
        $this->registerDefaultVariables($transformation, $proc, $structure);
        $this->setProcessorParameters($transformation, $proc);

        $artifact = $this->getArtifactPath($transformation);

        $this->checkForSpacesInPath($artifact);

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
                    PreXslWriterEvent::createInstance($this)->setElement($element)->setProgress(array($key+1, $count))
                );

                $proc->setParameter('', $element->nodeName, $element->nodeValue);
                $file_name = $transformation->getTransformer()->generateFilename(
                    $element->nodeValue
                );

                if (! $artifact) {
                    $url = $this->generateUrlForXmlElement($project, $element);
                    if ($url === false || $url[0] !== DIRECTORY_SEPARATOR) {
                        continue;
                    }

                    $filename = $transformation->getTransformer()->getTarget()
                        . str_replace('/', DIRECTORY_SEPARATOR, $url);
                } else {
                    $filename = str_replace('{$' . $element->nodeName . '}', $file_name, $artifact);
                }

                $relativeFileName = substr($filename, strlen($transformation->getTransformer()->getTarget()) + 1);
                $proc->setParameter('', 'root', str_repeat('../', substr_count($relativeFileName, '/')));

                $this->writeToFile($filename, $proc, $structure);
            }
        } else {
            if (substr($transformation->getArtifact(), 0, 1) == '$') {
                // not a file, it must become a variable!
                $variable_name = substr($transformation->getArtifact(), 1);
                $this->xsl_variables[$variable_name] = $proc->transformToXml($structure);
            } else {
                $relativeFileName = substr($artifact, strlen($transformation->getTransformer()->getTarget()) + 1);
                $proc->setParameter('', 'root', str_repeat('../', substr_count($relativeFileName, '/')));

                $this->writeToFile($artifact, $proc, $structure);
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
        // Windows requires an additional / after the scheme. If not provided then errno 22 (I/O Error: Invalid
        // Argument) will be raised. Thanks to @FnTmLV for finding the cause. See issue #284 for more information.
        // An exception to the above is when running from a Phar file; in this case the stream is handled as if on
        // linux; see issue #713 for more information on this exception.
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' && ! \Phar::running()) {
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
    public function setProcessorParameters(TransformationObject $transformation, $proc)
    {
        foreach ($this->xsl_variables as $key => $variable) {
            // XSL does not allow both single and double quotes in a string
            if ((strpos($variable, '"') !== false)
                && ((strpos($variable, "'") !== false))
            ) {
                $this->logger->warning(
                    'XSLT does not allow both double and single quotes in '
                    . 'a variable; transforming single quotes to a character '
                    . 'encoded version in variable: ' . $key
                );
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

    /**
     * @param $structureFilename
     * @return \DOMDocument
     */
    private function loadAst($structureFilename)
    {
        if (!is_readable($structureFilename)) {
            throw new \RuntimeException(
                'Structure.xml file was not found in the target directory, is the XML writer missing from the '
                . 'template definition?'
            );
        }

        $structure = new \DOMDocument('1.0', 'utf-8');
        libxml_use_internal_errors(true);
        $structure->load($structureFilename);

        if (empty($structure->documentElement)) {
            $message = 'Specified DOMDocument lacks documentElement, cannot transform.';
            $error = libxml_get_last_error();
            if ($error) {
                $message .= PHP_EOL . 'Apparently an error occurred with reading the structure.xml file, the reported '
                    . 'error was "' . trim($error->message) . '" on line ' . $error->line;
            }
            throw new Exception($message);
        }

        return $structure;
    }

    /**
     * @param Transformation $transformation
     * @param $proc
     * @param $structure
     */
    private function registerDefaultVariables(Transformation $transformation, $proc, $structure)
    {
        $proc->setParameter('', 'title', $structure->documentElement->getAttribute('title'));

        if ($transformation->getParameter('search') !== null && $transformation->getParameter('search')->getValue()) {
            $proc->setParameter('', 'search_template', $transformation->getParameter('search')->getValue());
        } else {
            $proc->setParameter('', 'search_template', 'none');
        }

        $proc->setParameter('', 'version', Application::$VERSION);
        $proc->setParameter('', 'generated_datetime', date('r'));
    }

    /**
     * @param $filename
     * @param $proc
     * @param $structure
     */
    private function writeToFile($filename, $proc, $structure)
    {
        if (!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }
        $proc->transformToURI($structure, $this->getXsltUriFromFilename($filename));
    }

    /**
     * @param Transformation $transformation
     * @return string
     */
    private function getAstPath(Transformation $transformation)
    {
        return $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . 'structure.xml';
    }

    /**
     * Returns the path to the location where the artifact should be written, or null to automatically detect the
     * location using the router.
     *
     * @param Transformation $transformation
     *
     * @return string|null
     */
    private function getArtifactPath(Transformation $transformation)
    {
        return $transformation->getArtifact()
            ? $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getArtifact()
            : null;
    }

    /**
     * @param ProjectDescriptor $project
     * @param $element
     * @return false|string
     */
    private function generateUrlForXmlElement(ProjectDescriptor $project, $element)
    {
        $elements = $project->getIndexes()->get('elements');

        $elementFqcn = ($element->parentNode->nodeName === 'namespace' ? '~\\' : '') . $element->nodeValue;
        $node = (isset($elements[$elementFqcn]))
            ? $elements[$elementFqcn]
            : $element->nodeValue; // do not use the normalized version if the element is not found!

        $rule = $this->routers->match($node);
        if (!$rule) {
            throw new \InvalidArgumentException(
                'No matching routing rule could be found for the given node, please provide an artifact location, '
                . 'encountered: ' . ($node === null ? 'NULL' : get_class($node))
            );
        }

        $rule = new ForFileProxy($rule);
        $url = $rule->generate($node);

        return $url;
    }
}
