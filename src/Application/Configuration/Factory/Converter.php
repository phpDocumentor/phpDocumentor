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
 * Interface that strategies for the ConfigurationFactory should implement.
 */
interface Converter
{
    /**
     * Converts the configuration xml to either an array or another xml.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return mixed
     */
    public function convert(\SimpleXMLElement $xml);

    /**
     * Checks if the strategy should handle the provided configuration xml.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return bool
     */
    public function match(\SimpleXMLElement $xml);
}
