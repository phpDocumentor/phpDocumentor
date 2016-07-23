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
final class ConfigurationConverter
{
    /**
     * Converts the phpDocumentor2 configuration file to the latest version.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return \SimpleXMLElement
     */
    public function convertToLatestVersion(\SimpleXMLElement $xml)
    {
        $priorSetting = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            switch ((string) $xml->attributes()->version) {
                case '': // version 2 has no version
                    $xml = $this->convertToVersion3($xml);
                    // no break
                case '3':
                    // for future use
                    // no break
            }

            return $xml;
        } finally {
            libxml_use_internal_errors($priorSetting);
        }
    }

    /**
     * Converts the configuration file from phpDocumentor2 to phpDocumentor3.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return \SimpleXMLElement
     */
    private function convertToVersion3(\SimpleXMLElement $xml)
    {
        $this->validateVersion2($xml);

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

        return $xmlResult;
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

    /**
     * Validates if the xml has a root element which name is phpdocumentor.
     *
     * @param \SimpleXMLElement $xml
     *
     * @throws \InvalidArgumentException if the root element of the xml is not phpdocumentor.
     */
    private function validateVersion2(\SimpleXMLElement $xml)
    {
        if ($xml->getName() !== 'phpdocumentor') {
            throw new \InvalidArgumentException(
                sprintf('Root element of the xml should be phpdocumentor, %s found.', $xml->getName())
            );
        }
    }
}
