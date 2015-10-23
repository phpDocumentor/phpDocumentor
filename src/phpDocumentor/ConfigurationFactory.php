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

namespace phpDocumentor;

use phpDocumentor\ConfigurationFactory\Strategy;

/**
 * The ConfigurationFactory converts the configuration xml from a Uri into an array.
 */
class ConfigurationFactory
{
    /**
     * The Uri that contains the path to the configuration file.
     *
     * @var Uri
     */
    private $uri;

    /**
     * The configuration xml.
     *
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * All strategies that are used by the ConfigurationFactory.
     *
     * @var Strategy[]
     */
    private $strategies = [];

    /**
     * Initializes the ConfigurationFactory.
     *
     * @param Strategy[] $strategies
     * @param Uri        $uri
     */
    public function __construct(array $strategies, Uri $uri)
    {
        foreach ($strategies as $strategy) {
            $this->registerStrategy($strategy);
        }

        $this->replaceLocation($uri);
    }

    /**
     * Replaces the location of the configuration file if it differs from the existing one.
     *
     * @param Uri $uri
     *
     * @return void
     */
    public function replaceLocation(Uri $uri)
    {
        if (!isset($this->uri) || !$this->uri->equals($uri)) {
            $this->xml = new \SimpleXMLElement($uri, 0, true);

            $this->uri = $uri;
        }
    }

    /**
     * Converts the phpDocumentor configuration xml to an array.
     *
     * @return array
     *
     * @throws \RuntimeException if no matching strategy can be found.
     */
    public function get()
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->match($this->xml) === true) {
                return $strategy->convert($this->xml);
            }
        };

        throw new \RuntimeException('No strategy found that matches the configuration xml');
    }

    /**
     * Adds strategies that are used in the ConfigurationFactory.
     *
     * @param Strategy $strategy
     *
     * @return void
     */
    private function registerStrategy(Strategy $strategy)
    {
        $this->strategies[] = $strategy;
    }
}
