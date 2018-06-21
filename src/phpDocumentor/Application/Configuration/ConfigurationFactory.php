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

namespace phpDocumentor\Application\Configuration;

use phpDocumentor\Application\Configuration\Factory\Strategy;
use phpDocumentor\Application\Configuration\Factory\Version3;
use phpDocumentor\DomainModel\Uri;
use RuntimeException;
use SimpleXMLElement;

/**
 * The ConfigurationFactory converts the configuration xml from a Uri into an array.
 */
final class ConfigurationFactory
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
     * @param callable[] $middlewares
     */
    public function __construct(array $strategies, Uri $uri, array $middlewares = [])
    {
        foreach ($strategies as $strategy) {
            $this->registerStrategy($strategy);
        }

        $this->replaceLocation($uri);
        $this->middlewares = $middlewares;
    }

    public static function createInstance(iterable $strategiesBuilder): self
    {
        $strategies = [];

        foreach ($strategiesBuilder as $stategy) {
            $strategies[] = $stategy;
        }

        return new static($strategies, new Uri('file://' . getcwd() . '/phpdoc.dist.xml'));
    }

    /**
     * Adds a middleware callback that allows the consumer to alter the configuration array when it is constructed.
     */
    public function addMiddleware(callable $middleware): void
    {
        $this->middlewares[] = $middleware;

        // Middleware's changed; we must rebuild the cache
        $this->clearCache();
    }

    /**
     * Replaces the location of the configuration file if it differs from the existing one.
     */
    public function replaceLocation(Uri $uri): void
    {
        if (!isset($this->uri) || !$this->uri->equals($uri)) {
            $this->uri = $uri;
            $this->clearCache();
        }
    }

    /**
     * Converts the phpDocumentor configuration xml to an array.
     *
     * @return string[]
     * @throws RuntimeException if no matching strategy can be found.
     */
    public function get(): array
    {
        if ($this->cachedConfiguration) {
            return $this->cachedConfiguration;
        }

        $file = (string) $this->uri;
        if (file_exists($file)) {
            $xml = new SimpleXMLElement($file, 0, true);
            $this->cachedConfiguration = $this->extractConfigurationArray($xml);
        } else {
            $this->cachedConfiguration = Version3::buildDefault();
        }

        $this->applyMiddleware();

        return $this->cachedConfiguration;
    }

    /**
     * Clears the cache for the configuration.
     */
    public function clearCache(): void
    {
        $this->cachedConfiguration = null;
    }

    /**
     * Adds strategies that are used in the ConfigurationFactory.
     */
    private function registerStrategy(Strategy $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    /**
     * Converts the given XML structure into an array containing the configuration.
     *
     * @param SimpleXMLElement $xml
     * @return array
     * @throws RuntimeException
     */
    private function extractConfigurationArray(SimpleXMLElement $xml): array
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->match($xml) === true) {
                return $strategy->convert($xml);
            }
        }

        throw new RuntimeException('No strategy found that matches the configuration xml');
    }

    /**
     * Applies all middleware callbacks onto the configuration.
     */
    private function applyMiddleware(): void
    {
        foreach ($this->middlewares as $middleware) {
            $this->cachedConfiguration = $middleware($this->cachedConfiguration);
        }
    }
}
