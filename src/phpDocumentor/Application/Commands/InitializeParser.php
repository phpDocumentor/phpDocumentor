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

namespace phpDocumentor\Application\Commands;

use phpDocumentor\Configuration;

/**
 * Prepares the system to initialize the parser using the given configuration.
 */
final class InitializeParser
{
    /** @var Configuration */
    private $configuration;

    /**
     * Initializes the parser with the given configuration.
     *
     * @param Configuration $configuration
     */
    public function __construct($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Returns the configuration that is used to initialize the parser.
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
