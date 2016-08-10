<?php

namespace phpDocumentor\Application\Configuration\Factory;

final class Version2To3Converter extends BaseConverter
{
    public function __construct(Converter $converter = null)
    {
        $this->nextConverter = $converter;
    }

    public function match(\SimpleXMLElement $xml)
    {
        return !isset($xml->attributes()->version);
    }

    protected function innerConvert(\SimpleXMLElement $xml)
    {
        $this->validate($xml);

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
     * Validates if the xml has a root element which name is phpdocumentor.
     *
     * @param \SimpleXMLElement $xml
     *
     * @throws \InvalidArgumentException if the root element of the xml is not phpdocumentor.
     */
    private function validate(\SimpleXMLElement $xml)
    {
        if ($xml->getName() !== 'phpdocumentor') {
            throw new \InvalidArgumentException(
                sprintf('Root element of the xml should be phpdocumentor, %s found.', $xml->getName())
            );
        }
    }

    /**
     * @return \DOMDocument
     */
    private function getXsl()
    {
        $xsl  = new \DOMDocument();
        $data = file_get_contents(__DIR__ . '/../phpDocumentor3.xsl');

        $xsl->loadXML($data);

        return $xsl;
    }
}
