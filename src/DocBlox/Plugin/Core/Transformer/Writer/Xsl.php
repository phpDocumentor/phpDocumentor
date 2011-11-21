<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * XSL transformation writer; generates static HTML out of the structure and XSL
 * templates.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Plugin_Core_Transformer_Writer_Xsl
    extends DocBlox_Transformer_Writer_Abstract
{
    protected $xsl_variables = array();

    /**
     * This method combines the structure.xml and the given target template
     * and creates a static html page at the artifact location.
     *
     * @param DOMDocument                        $structure      XML source.
     * @param DocBlox_Transformer_Transformation $transformation Transformation.
     *
     * @throws Exception
     *
     * @return void
     */
    public function transform(
        DOMDocument $structure,
        DocBlox_Transformer_Transformation $transformation
    ) {
        if (!class_exists('XSLTProcessor')) {
            throw new DocBlox_Transformer_Exception(
                'The XSL writer was unable to find your XSLTProcessor; '
                . 'please check if you have installed the PHP XSL extension'
            );
        }

        $artifact = $transformation->getTransformer()->getTarget()
        . DIRECTORY_SEPARATOR . $transformation->getArtifact();

        $xsl = new DOMDocument();
        $xsl->load($transformation->getSourceAsPath());

        $proc = new XSLTProcessor();
        $proc->importStyleSheet($xsl);
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
        $proc->setParameter(
            '', 'version', DocBlox_Core_Abstract::VERSION
        );

        // check parameters for variables and add them when found
        $this->setProcessorParameters($transformation, $proc);

        // if a query is given, then apply a transformation to the artifact
        // location by replacing ($<var>} with the sluggified node-value of the
        // search result
        if ($transformation->getQuery() !== '') {
            $xpath = new DOMXPath($transformation->getTransformer()->getSource());

            /** @var DOMNodeList $qry */
            $qry = $xpath->query($transformation->getQuery());
            $count = $qry->length;
            foreach ($qry as $key => $element) {
                $this->dispatch(
                    'transformer.writer.xsl.pre',
                    array(
                         'element' => $element,
                         'progress' => array($key+1, $count)
                    )
                );

                $proc->setParameter('', $element->nodeName, $element->nodeValue);
                $filename = str_replace(
                    '{$' . $element->nodeName . '}',
                    $transformation->getTransformer()->generateFilename(
                        $element->nodeValue
                    ),
                    $artifact
                );
                $this->log(
                    'Processing the file: ' . $element->nodeValue
                    . ' as ' . $filename
                );
                $proc->transformToURI($structure, 'file://' . $filename);
            }
        } else {
            if (substr($transformation->getArtifact(), 0, 1) == '$') {
                // not a file, it must become a variable!
                $variable_name = substr($transformation->getArtifact(), 1);
                $this->xsl_variables[$variable_name]
                    = $proc->transformToXml($structure);
            } else {
                $proc->transformToURI($structure, 'file://' . $artifact);
            }
        }
    }

    /**
     * Sets the parameters of the XSLT processor.
     *
     * @param DocBlox_Transformer_Transformation $transformation Transformation.
     * @param XSLTProcessor                      $proc           XSLTProcessor.
     *
     * @return void
     */
    public function setProcessorParameters(
        DocBlox_Transformer_Transformation $transformation,
        XSLTProcessor $proc
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
                    Zend_Log::WARN
                );
                $variable = str_replace("'", "&#39;", $variable);
            }

            $proc->setParameter('', $key, $variable);
        }

        // add / overwrite the parameters with those defined in the
        // transformation entry
        $parameters = $transformation->getParameters();
        if (isset($parameters['variables'])) {
            /** @var DOMElement $variable */
            foreach ($parameters['variables'] as $key => $value) {
                $proc->setParameter('', $key, $value);
            }
        }
    }

}