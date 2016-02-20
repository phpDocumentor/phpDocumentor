<?php

namespace phpDocumentor\Application\Renderer;

use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\ReadModel\ReadModel;
use phpDocumentor\Application\Application;
use phpDocumentor\Application\Renderer\Template\Action;
use phpDocumentor\Application\Renderer\Template\Action\Xsl;
use phpDocumentor\Infrastructure\Renderer\Template\LocalPathsRepository;
use phpDocumentor\DomainModel\Renderer\Router\ForFileProxy;
use phpDocumentor\DomainModel\Renderer\Router\RouterAbstract;

class XslRenderer
{
    public function render(ReadModel $view, Path $destination, $template = null)
    {
        // TODO: Implement render() method.
    }

    /** @var string[] */
    protected $xsl_variables = array();

    /** @var RouterAbstract */
    private $routers;

    /** @var Analyzer */
    private $analyzer;

    /** @var LocalPathsRepository */
    private $fileRepository;

    public function __construct(Analyzer $analyzer, RouterAbstract $router, LocalPathsRepository $fileRepository)
    {
        $this->analyzer = $analyzer;
        $this->routers  = $router;
        $this->fileRepository = $fileRepository;
    }

    /**
     * This method combines the structure.xml and the given target template
     * and creates a static html page at the artifact location.
     *
     * @param Xsl $action
     *
     * @throws \RuntimeException if the structure.xml file could not be found.
     * @throws \Exception        if the structure.xml file's documentRoot could not be read because of encoding issues
     *    or because it was absent.
     *
     * @return void
     */
    public function __invoke(Action $action)
    {
        $structure = $this->loadAst($this->getAstPath($action->getRenderPass()->getDestination()));
        $project = $this->analyzer->getProjectDescriptor();

        $view = $this->fileRepository->findByTemplateAndPath($action->getTemplate(), $action->getView());
        $proc = $this->getXslProcessor((string)$view);
        $proc->registerPHPFunctions();
        $this->registerDefaultVariables($proc, $structure);
        $this->setProcessorParameters($proc);

        $artifact = $action->getDestination()
            ? $action->getRenderPass()->getDestination() . '/' . $action->getDestination()
            : null;

        // if a query is given, then apply a transformation to the artifact
        // location by replacing ($<var>} with the sluggified node-value of the
        // search result
        if ($action->getQuery() !== '') {
            $xpath = new \DOMXPath($structure);

            /** @var \DOMNodeList $qry */
            $qry = $xpath->query($action->getQuery());
            foreach ($qry as $key => $element) {
                $proc->setParameter('', $element->nodeName, $element->nodeValue);
                $file_name = $this->generateFilename($element->nodeValue);

                if (! $artifact) {
                    $url = $this->generateUrlForXmlElement($project, $element);
                    if ($url === false || $url[0] !== DIRECTORY_SEPARATOR) {
                        continue;
                    }

                    $filename = $action->getRenderPass()->getDestination()
                        . str_replace('/', DIRECTORY_SEPARATOR, $url);
                } else {
                    $filename = str_replace('{$' . $element->nodeName . '}', $file_name, $artifact);
                }

                $relativeFileName = substr($filename, strlen($action->getRenderPass()->getDestination()) + 1);
                $proc->setParameter('', 'root', str_repeat('../', substr_count($relativeFileName, '/')));

                $this->writeToFile($filename, $proc, $structure);
            }
        } else {
            if (substr($action->getDestination(), 0, 1) == '$') {
                // not a file, it must become a variable!
                $variable_name = substr($action->getDestination(), 1);
                $this->xsl_variables[$variable_name] = $proc->transformToXml($structure);
            } else {
                $relativeFileName = substr($artifact, strlen($action->getRenderPass()->getDestination()) + 1);
                $proc->setParameter('', 'root', str_repeat('../', substr_count($relativeFileName, '/')));

                $this->writeToFile($artifact, $proc, $structure);
            }
        }
    }

    /**
     * Converts a source file name to the name used for generating the end result.
     *
     * This method strips down the given $name using the following rules:
     *
     * * if the $name is suffixed with .php then that is removed
     * * any occurrence of \ or DIRECTORY_SEPARATOR is replaced with .
     * * any dots that the name starts or ends with is removed
     * * the result is suffixed with .html
     *
     * @param string $name Name to convert.
     *
     * @return string
     */
    private function generateFilename($name)
    {
        if (substr($name, -4) == '.php') {
            $name = substr($name, 0, -4);
        }

        return trim(str_replace(array(DIRECTORY_SEPARATOR, '\\'), '.', trim($name, DIRECTORY_SEPARATOR . '.')), '.')
        . '.html';
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
        if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' && (! method_exists('Phar', 'running') ||  ! \Phar::running())) {
            $filename = '/' . $filename;
        }

        return 'file://' . $filename;
    }

    /**
     * Sets the parameters of the XSLT processor.
     *
     * @param \XSLTProcessor $proc XSLTProcessor.
     *
     * @return void
     */
    public function setProcessorParameters($proc)
    {
        foreach ($this->xsl_variables as $key => $variable) {
            // XSL does not allow both single and double quotes in a string
            if ((strpos($variable, '"') !== false)
                && ((strpos($variable, "'") !== false))
            ) {
                trigger_error(
                    'XSLT does not allow both double and single quotes in '
                    . 'a variable; transforming single quotes to a character '
                    . 'encoded version in variable: ' . $key,
                    E_USER_WARNING
                );
                $variable = str_replace("'", "&#39;", $variable);
            }

            $proc->setParameter('', $key, $variable);
        }

        // add / overwrite the parameters with those defined in the
        // transformation entry
        // TODO: Re-add differently
//        $parameters = $transformation->getParameters();
//        if (isset($parameters['variables'])) {
//            /** @var \DOMElement $variable */
//            foreach ($parameters['variables'] as $key => $value) {
//                $proc->setParameter('', $key, $value);
//            }
//        }
    }

    /**
     *
     *
     * @param string $source
     *
     * @return \XSLTCache|\XSLTProcessor
     */
    protected function getXslProcessor($source)
    {
        $xslTemplatePath = $source;
        if (!file_exists($xslTemplatePath)) {
            throw new \Exception('Unable to find XSL template "' . $xslTemplatePath . '"');
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
            throw new \Exception($message);
        }

        return $structure;
    }

    /**
     * @param \XSLTProcessor $proc
     * @param \DOMDocument $structure
     */
    private function registerDefaultVariables($proc, $structure)
    {
        $proc->setParameter('', 'title', $structure->documentElement->getAttribute('title'));
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
     * @param string $target
     * @return string
     */
    private function getAstPath($target)
    {
        return $target . DIRECTORY_SEPARATOR . 'structure.xml';
    }

    /**
     * @param ProjectInterface $project
     * @param $element
     * @return false|string
     */
    private function generateUrlForXmlElement(ProjectInterface $project, $element)
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
