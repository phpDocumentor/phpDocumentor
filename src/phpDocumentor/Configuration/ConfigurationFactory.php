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

namespace phpDocumentor\Configuration;

use phpDocumentor\Configuration\Factory\Strategy;
use phpDocumentor\Configuration\Factory\Version3;
use phpDocumentor\DomainModel\Uri;
use RuntimeException;

/**
 * The ConfigurationFactory converts the configuration xml from a Uri into an array.
 */
final class ConfigurationFactory
{
    /**
     * @var Strategy[] All strategies that are used by the ConfigurationFactory.
     */
    private $strategies = [];

    /**
     * A series of callables that take the configuration array as parameter and should return that array or a modified
     * version of it.
     *
     * @var callable[]
     */
    private $middlewares = [];
    private $defaultFiles = [];

    /**
     * Initializes the ConfigurationFactory.
     *
     * @param Strategy[]|iterable $strategies
     * @param array|null $defaultFiles
     */
    public function __construct(iterable $strategies, array $defaultFiles = null)
    {
        if ($defaultFiles === null) {
            $defaultFiles = [
                getcwd() . '/phpdoc.xml',
                getcwd() . '/phpdoc.dist.xml',
                getcwd() . '/phpdoc.xml.dist'
            ];
        }

        foreach ($strategies as $strategy) {
            $this->registerStrategy($strategy);
        }
        $this->defaultFiles = $defaultFiles;
    }

    /**
     * Adds a middleware callback that allows the consumer to alter the configuration array when it is constructed.
     *
     * @param callable $middleware
     */
    public function addMiddleware(callable $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Attempts to load a configuration from the default locations for phpDocumentor
     *
     * @return Configuration
     */
    public function fromDefaultLocations(): Configuration
    {
        foreach ($this->defaultFiles as $file) {
            try {
                return $this->fromUri(new Uri($file));
            } catch (\InvalidArgumentException $e) {
                continue;
            }
        }

        return new Configuration($this->applyMiddleware(Version3::buildDefault()));
    }

    /**
     * Converts the phpDocumentor configuration xml to an array.
     *
     * @param Uri $uri The location of the file to be loaded.
     *
     * @return Configuration
     * @throws RuntimeException if no matching strategy can be found.
     */
    public function fromUri(Uri $uri): Configuration
    {
        $filename = (string) $uri;

        if (!file_exists($filename)) {
            throw new \InvalidArgumentException(sprintf('File %s could not be found', $filename));
        }

        return new Configuration($this->applyMiddleware($this->fromFile($filename)));
    }

    /**
     * Adds strategies that are used in the ConfigurationFactory.
     *
     * @param Strategy $strategy
     */
    private function registerStrategy(Strategy $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    /**
     * Converts the given XML structure into an array containing the configuration.
     *
     * @param string $file the (xml) file that we try to import.
     *
     * @return array a structure containing all configuration options.
     *
     * @throws RuntimeException
     */
    private function fromFile(string $file): array
    {
        $xml = new \SimpleXMLElement($file, 0, true);
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($xml) === true) {
                return $strategy->convert($xml);
            }
        }

        throw new RuntimeException('No strategy found that matches the configuration xml');
    }

    /**
     * Applies all middleware callbacks onto the configuration.
     *
     * @param array $configuration
     *
     * @return array
     */
    private function applyMiddleware(array $configuration): array
    {
        foreach ($this->middlewares as $middleware) {
            $configuration = $middleware($configuration);
        }

        return $configuration;
    }
}
