<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Configuration;

use phpDocumentor\DomainModel\Uri;

/**
 * The ConfigurationFactory converts the configuration xml from a Uri into an array.
 */
class ConfigurationConverter
{
    /**
     * @var Uri The Uri that contains the path to the configuration file.
     */
    private $uri;

    /**
     * @var string The path to the xsd that is used for validation of the configuration file.
     */
    private $schemaPath;

    /**
     * Initializes the ConfigurationConverter.
     *
     * @param Uri    $uri
     * @param string $schemaPath
     */
    public function __construct(Uri $uri, $schemaPath)
    {
        $this->uri        = $uri;
        $this->schemaPath = $schemaPath;
    }

    /**
     * Converts the phpDocumentor2 configuration file.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return \SimpleXMLElement
     */
    public function convert(\SimpleXMLElement $xml)
    {
        $priorSetting = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $XSLTProcessor = new \XSLTProcessor();
        $XSLTProcessor->importStylesheet($this->getXsl());
        $result = $XSLTProcessor->transformToXml($xml);

        if ($result === false) {
            throw new \RuntimeException('Could not convert the xml. ' . libxml_get_last_error()->message);
        }

        $xmlResult = new \SimpleXMLElement($result);

        $this->validate($xmlResult);

        libxml_clear_errors();
        libxml_use_internal_errors($priorSetting);

        return $xmlResult;
    }

    /**
     * Validates the configuration xml structure against the schema defined in the schemaPath.
     *
     * @param \SimpleXMLElement $phpDocumentor
     *
     * @return void
     * @throws \InvalidArgumentException if the xml structure is not valid.
     */
    private function validate(\SimpleXMLElement $phpDocumentor)
    {
        $priorSetting = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $dom        = new \DOMDocument();
        $domElement = dom_import_simplexml($phpDocumentor);
        $domElement = $dom->importNode($domElement, true);
        $dom->appendChild($domElement);

        $dom->schemaValidate($this->schemaPath);

        $error = libxml_get_last_error();

        if ($error !== false) {
            throw new \InvalidArgumentException($error->message);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($priorSetting);
    }

    /**
     * @return \DOMDocument
     */
    private function getXsl()
    {
        $xsl  = new \DOMDocument();
        $data = file_get_contents(__DIR__ . '/style.xsl');
        $xsl->loadXML($data);

        return $xsl;
    }
}
