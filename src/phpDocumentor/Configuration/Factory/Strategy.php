<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Configuration\Factory;

use SimpleXMLElement;

/**
 * Interface that strategies for the ConfigurationFactory should implement.
 */
interface Strategy
{
    /**
     * Converts the configuration xml to an array.
     */
    public function convert(SimpleXMLElement $xml): array;

    /**
     * Checks if the strategy should handle the provided configuration xml.
     */
    public function supports(SimpleXMLElement $xml): bool;
}
