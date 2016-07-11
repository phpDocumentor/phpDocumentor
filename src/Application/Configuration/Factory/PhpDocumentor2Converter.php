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

namespace phpDocumentor\Application\Configuration\Factory;

/**
 * phpDocumentor2 converter for converting the configuration xml to a phpDocumentor3 xml.
 */
final class PhpDocumentor2Converter extends BaseConverter implements Converter
{
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
            $errors = [];
            foreach (libxml_get_errors() as $error) {
                $errors[] = $error->message;
            }

            throw new \RuntimeException('Could not convert the xml. ' . implode('; ', $errors));
        }

        $xmlResult = new \SimpleXMLElement($result);

        libxml_clear_errors();
        libxml_use_internal_errors($priorSetting);

        return $xmlResult;
    }

    /**
     * @inheritdoc
     */
    public function match(\SimpleXMLElement $phpDocumentor)
    {
        return !isset($phpDocumentor->attributes()->version);
    }

    /**
     * @return \DOMDocument
     */
    private function getXsl()
    {
        $xsl  = new \DOMDocument();
        $data = file_get_contents(__DIR__ . '/../style.xsl');

        $xsl->loadXML($data);

        return $xsl;
    }
}
