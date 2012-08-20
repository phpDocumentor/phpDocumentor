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

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

/**
 * XSL transformation writer; generates static HTML out of the structure and XSL
 * templates.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Xsl extends \phpDocumentor\Transformer\Writer\WriterAbstract
{
    protected $xsl_variables = array();

    /**
     * This method combines the structure.xml and the given target template
     * and creates a static html page at the artifact location.
     *
     * @param \DOMDocument                        $structure      XML source.
     * @param \phpDocumentor\Transformer\Transformation $transformation Transformation.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function transform(
        \DOMDocument $structure,
        \phpDocumentor\Transformer\Transformation $transformation
    ) {
        if (!class_exists('XSLTProcessor')) {
            throw new \phpDocumentor\Plugin\Core\Exception(
                'The XSL writer was unable to find your XSLTProcessor; '
                . 'please check if you have installed the PHP XSL extension'
            );
        }

        $artifact = $transformation->getTransformer()->getTarget()
        . DIRECTORY_SEPARATOR . $transformation->getArtifact();

        $xsl = new \DOMDocument();
        $xsl->load($transformation->getSourceAsPath());

        $proc = new \XSLTProcessor();
        $proc->importStyleSheet($xsl);
        if (empty($structure->documentElement)) {
            throw new \phpDocumentor\Plugin\Core\Exception(
                'Specified DOMDocument lacks documentElement, cannot transform'
            );
        }

        $proc->setParameter(
            '', 'title', $structure->documentElement->getAttribute('title')
        );
        $proc->setParameter(
            '', 'root',
            str_repeat('../', substr_count($transformation->getArtifact(), '/'))
        );
        $proc->setParameter(
            '', 'search_template', $transformation->getParameter('search', 'none')
        );
        $proc->setParameter('', 'version', \phpDocumentor\Application::VERSION);
        $proc->setParameter(
            '', 'generated_datetime', date('r')
        );


        // check parameters for variables and add them when found
        $this->setProcessorParameters($transformation, $proc);

        // if a query is given, then apply a transformation to the artifact
        // location by replacing ($<var>} with the sluggified node-value of the
        // search result
        if ($transformation->getQuery() !== '') {
            $xpath = new \DOMXPath($transformation->getTransformer()->getSource());

            /** @var \DOMNodeList $qry */
            $qry = $xpath->query($transformation->getQuery());
            $count = $qry->length;
            foreach ($qry as $key => $element) {
                \phpDocumentor\Event\Dispatcher::getInstance()->dispatch(
                    'transformer.writer.xsl.pre',
                    \phpDocumentor\Transformer\Event\PreXslWriterEvent
                    ::createInstance($this)->setElement($element)
                    ->setProgress(array($key+1, $count))
                );

                $proc->setParameter('', $element->nodeName, $element->nodeValue);
                $file_name = $transformation->getTransformer()->generateFilename(
                    $element->nodeValue
                );

                $filename = str_replace(
                    '{$' . $element->nodeName . '}', $file_name, $artifact
                );
                $this->log(
                    'Processing the file: ' . $element->nodeValue
                    . ' as ' . $filename
                );

                if (!file_exists(dirname($filename))) {
                    mkdir(dirname($filename), 0755, true);
                }
                $proc->transformToURI(
                    $structure, $this->getXsltUriFromFilename($filename)
                );
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
                $proc->transformToURI(
                    $structure, $this->getXsltUriFromFilename($artifact)
                );
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
     * @param \phpDocumentor\Transformer\Transformation $transformation Transformation.
     * @param \XSLTProcessor                      $proc           XSLTProcessor.
     *
     * @return void
     */
    public function setProcessorParameters(
        \phpDocumentor\Transformer\Transformation $transformation,
        \XSLTProcessor $proc
    ) {
        foreach ($this->xsl_variables as $key => $variable) {
            // XSL does not allow both single and double quotes in a string
            if ((strpos($variable, '"') !== false)
                && ((strpos($variable, "'") !== false))
            ) {
                $this->log(
                    'XSLT does not allow both double and single quotes in '
                    . 'a variable; transforming single quotes to a character '
                    . 'encoded version in variable: ' . $key,
                    \phpDocumentor\Plugin\Core\Log::WARN
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

}
