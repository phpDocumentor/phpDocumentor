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
final class ConfigurationConverter extends BaseConverter implements Converter
{
    public function __construct(Converter $converter = null)
    {
        $this->nextConverter = $converter;
    }

    /**
     * @inheritdoc
     */
    public function convert(\SimpleXMLElement $xml)
    {
        if (!$this->match($xml)) {
             $xml = $this->nextConverter->convert($xml);
        }

        return $this->innerConvert($xml);
    }

    /**
     * Converts the phpDocumentor configuration file to the latest version.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return \SimpleXMLElement
     */
    protected function innerConvert(\SimpleXMLElement $xml)
    {
        $priorSetting = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            switch ((string) $xml->attributes()->version) {
                case '': // version 2 has no version
                    $xml = $this->convert($xml);
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

    protected function match(\SimpleXMLElement $xml)
    {
        return !isset($xml->attributes()->version);
    }
}
