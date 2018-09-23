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
     */
    public function __construct(iterable $strategies)
    {
        foreach ($strategies as $strategy) {
            $this->registerStrategy($strategy);
        }
    }

    /**
     * Adds a middleware callback that allows the consumer to alter the configuration array when it is constructed.
     */
    public function addMiddleware(callable $middleware): void
    {
        $this->middlewares[] = $middleware;
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
        $file = (string) $uri;

        if (!file_exists($file)) {
            throw new \InvalidArgumentException(sprintf('File %s could not be found', $file));
        }

        return new Configuration(
            $this->applyMiddleware(
                $this->extractConfigurationArray(new SimpleXMLElement($file, 0, true))
            )
        );
    }

    /**
     * Attempts to load a configuration from
     */
    public function fromDefaultLocations(): Configuration
    {
        $files = [
            'file://' . getcwd() . '/phpdoc.xml',
            'file://' . getcwd() . '/phpdoc.dist.xml',
            'file://' . getcwd() . '/phpdoc.xml.dist'
        ];

        foreach ($files as $file) {
            try {
                return $this->fromUri(new Uri($file));
            } catch (\InvalidArgumentException $e) {
                continue;
            }
        }

        return new Configuration($this->applyMiddleware(Version3::buildDefault()));
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
     * @throws RuntimeException
     */
    private function extractConfigurationArray(SimpleXMLElement $xml): array
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($xml) === true) {
                return $strategy->convert($xml);
            }
        }

        throw new RuntimeException('No strategy found that matches the configuration xml');
    }

    /**
     * Applies all middleware callbacks onto the configuration.
     */
    private function applyMiddleware(array $configuration): array
    {
        foreach ($this->middlewares as $middleware) {
            $configuration = $middleware($configuration);
        }

        return $configuration;
    }
}
