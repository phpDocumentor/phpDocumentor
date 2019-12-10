<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Configuration;

use phpDocumentor\Configuration\Exception\InvalidConfigPathException;
use phpDocumentor\Configuration\Factory\Strategy;
use phpDocumentor\Configuration\Factory\Version3;
use phpDocumentor\Uri;
use RuntimeException;
use SimpleXMLElement;
use function file_exists;
use function sprintf;

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

    /** @var string[] */
    private $defaultFiles = [];

    /** @var SymfonyConfigFactory */
    private $symfonyConfigFactory;

    /**
     * Initializes the ConfigurationFactory.
     *
     * @param Strategy[]|iterable $strategies
     * @param array               $defaultFiles
     */
    public function __construct(iterable $strategies, array $defaultFiles, SymfonyConfigFactory $symfonyConfigFactory)
    {
        foreach ($strategies as $strategy) {
            $this->registerStrategy($strategy);
        }
        $this->defaultFiles = $defaultFiles;
        $this->symfonyConfigFactory = $symfonyConfigFactory;
    }

    /**
     * Adds a middleware callback that allows the consumer to alter the configuration array when it is constructed.
     */
    public function addMiddleware(callable $middleware) : void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Attempts to load a configuration from the default locations for phpDocumentor
     */
    public function fromDefaultLocations() : Configuration
    {
        foreach ($this->defaultFiles as $file) {
            try {
                return $this->fromUri(new Uri($file));
            } catch (InvalidConfigPathException $e) {
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
     * @throws RuntimeException If no matching strategy can be found.
     * @throws InvalidConfigPathException If $uri points to an inexistent file.
     */
    public function fromUri(Uri $uri) : Configuration
    {
        $filename = (string) $uri;

        if (!file_exists($filename)) {
            throw new InvalidConfigPathException(sprintf('File %s could not be found', $filename));
        }

        $config = $this->symfonyConfigFactory->create($filename);
        var_dump($config);
        die();



        $xml = new SimpleXMLElement($filename, 0, true);
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($xml) !== true) {
                continue;
            }

            return new Configuration($this->applyMiddleware($strategy->convert($xml)));
        }

        throw new RuntimeException('No supported configuration files were found');
    }

    /**
     * Adds strategies that are used in the ConfigurationFactory.
     */
    private function registerStrategy(Strategy $strategy) : void
    {
        $this->strategies[] = $strategy;
    }

    /**
     * Applies all middleware callbacks onto the configuration.
     *
     * @param array $configuration
     *
     * @return array
     */
    private function applyMiddleware(array $configuration) : array
    {
        foreach ($this->middlewares as $middleware) {
            $configuration = $middleware($configuration);
        }

        return $configuration;
    }
}
