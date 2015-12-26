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

use phpDocumentor\Application\Configuration\Factory\Strategy;
use phpDocumentor\Uri;

/**
 * The ConfigurationFactory converts the configuration xml from a Uri into an array.
 */
class ConfigurationFactory
{
    /** @var Uri The Uri that contains the path to the configuration file. */
    private $uri;

    /** @var string[] The cached configuration as an array so that we improve performance */
    private $cachedConfiguration = [];

    /** @var Strategy[] All strategies that are used by the ConfigurationFactory. */
    private $strategies = [];

    /**
     * A series of callables that take the configuration array as parameter and should return that array or a modified
     * version of it.
     *
     * @var callable[]
     */
    private $middlewares = [];

    /**
     * Initializes the ConfigurationFactory.
     *
     * @param Strategy[] $strategies
     * @param Uri        $uri
     */
    public function __construct(array $strategies, Uri $uri, $middlewares = [])
    {
        foreach ($strategies as $strategy) {
            $this->registerStrategy($strategy);
        }

        $this->replaceLocation($uri);
        $this->middlewares = $middlewares;
    }

    /**
     * Adds a middleware callback that allows the consumer to alter the configuration array when it is constructed.
     *
     * @param callable $middleware
     *
     * @return void
     */
    public function addMiddleware(callable $middleware)
    {
        $this->middlewares[] = $middleware;

        // Middleware's changed; we must rebuild the cache
        $this->clearCache();
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
            $this->uri = $uri;
            $this->clearCache();
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
        if ($this->cachedConfiguration) {
            return $this->cachedConfiguration;
        }

        $xml = new \SimpleXMLElement($this->uri, 0, true);
        $this->cachedConfiguration = $this->extractConfigurationArray($xml);
        $this->applyMiddleware();

        return $this->cachedConfiguration;
    }

    /**
     * Clears the cache for the configuration.
     *
     * @return null
     */
    public function clearCache()
    {
        return $this->cachedConfiguration = null;
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

    /**
     * Converts the given XML structure into an array containing the configuration.
     *
     * @param \SimpleXMLElement $xml
     *
     * @return array
     */
    private function extractConfigurationArray($xml)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->match($xml) === true) {
                return $strategy->convert($xml);
            }
        };

        throw new \RuntimeException('No strategy found that matches the configuration xml');
    }

    /**
     * Applies all middleware callbacks onto the configuration.
     */
    private function applyMiddleware()
    {
        foreach ($this->middlewares as $middleware) {
            $this->cachedConfiguration = $middleware($this->cachedConfiguration);
        }
    }
}
