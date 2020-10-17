<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Configuration;

use League\Uri\Contracts\UriInterface;
use phpDocumentor\Configuration\Exception\InvalidConfigPathException;
use phpDocumentor\UriFactory;
use function file_exists;
use function sprintf;

/**
 * The ConfigurationFactory converts the configuration xml from a Uri into an array.
 */
/*final*/ class ConfigurationFactory
{
    /**
     * A series of callables that take the configuration array as parameter and should return that array or a modified
     * version of it.
     *
     * @var list<MiddlewareInterface>
     */
    private $middlewares = [];

    /** @var string[] */
    private $defaultFiles;

    /** @var SymfonyConfigFactory */
    private $symfonyConfigFactory;

    /**
     * Initializes the ConfigurationFactory.
     *
     * @param array<string> $defaultFiles
     */
    public function __construct(array $defaultFiles, SymfonyConfigFactory $symfonyConfigFactory)
    {
        $this->defaultFiles = $defaultFiles;
        $this->symfonyConfigFactory = $symfonyConfigFactory;
    }

    /**
     * Adds a middleware callback that allows the consumer to alter the configuration array when it is constructed.
     */
    public function addMiddleware(MiddlewareInterface $middleware) : void
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
                return $this->fromUri(UriFactory::createUri($file));
            } catch (InvalidConfigPathException $e) {
                continue;
            }
        }

        return new Configuration($this->applyMiddleware($this->createDefault()->getArrayCopy(), null));
    }

    public function createDefault() : Configuration
    {
        return new Configuration($this->symfonyConfigFactory->createDefault());
    }

    /**
     * Converts the phpDocumentor configuration xml to an array.
     *
     * @param UriInterface $uri The location of the file to be loaded.
     *
     * @throws InvalidConfigPathException If $uri points to an inexistent file.
     */
    public function fromUri(UriInterface $uri) : Configuration
    {
        $filename = (string) $uri;

        if (!file_exists($filename)) {
            throw new InvalidConfigPathException(sprintf('File %s could not be found', $filename));
        }

        $config = $this->symfonyConfigFactory->createFromFile($filename);

        return new Configuration($this->applyMiddleware($config, $uri));
    }

    public function fromDefault() : Configuration
    {
        return new Configuration($this->applyMiddleware($this->createDefault()->getArrayCopy(), null));
    }

    /**
     * Applies all middleware callbacks onto the configuration.
     *
     * @param array<string, string|array<mixed>> $configuration
     *
     * @return array<string, string|array<mixed>>
     */
    private function applyMiddleware(array $configuration, ?UriInterface $uri) : array
    {
        foreach ($this->middlewares as $middleware) {
            $configuration = $middleware($configuration, $uri);
        }

        return $configuration;
    }
}
