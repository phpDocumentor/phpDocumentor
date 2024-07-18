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
use phpDocumentor\Configuration\Definition\Version3;
use phpDocumentor\Configuration\Exception\InvalidConfigPathException;
use phpDocumentor\UriFactory;

use function array_map;
use function file_exists;
use function ltrim;
use function sprintf;

/**
 * The ConfigurationFactory converts the configuration xml from a Uri into an array.
 *
 * @psalm-import-type ConfigurationMap from Version3 as Version3ConfigurationMap
 * @psalm-type ConfigurationMap = array{
 *     phpdocumentor: Version3ConfigurationMap
 * }
 */
/*final*/ class ConfigurationFactory
{
    /**
     * A series of callables that take the configuration array as parameter and should return that array or a modified
     * version of it.
     *
     * @var list<MiddlewareInterface>
     */
    private array $middlewares = [];

    /**
     * Initializes the ConfigurationFactory.
     *
     * @param array<string> $defaultFiles
     */
    public function __construct(
        private readonly array $defaultFiles,
        private readonly SymfonyConfigFactory $symfonyConfigFactory,
    ) {
    }

    /**
     * Adds a middleware callback that allows the consumer to alter the configuration array when it is constructed.
     */
    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Attempts to load a configuration from the default locations for phpDocumentor
     */
    public function fromDefaultLocations(): Configuration
    {
        foreach ($this->defaultFiles as $file) {
            try {
                return $this->fromUri(UriFactory::createUri($file));
            } catch (InvalidConfigPathException) {
                continue;
            }
        }

        return new Configuration($this->applyMiddleware($this->createDefault(), null));
    }

    public function createDefault(): Configuration
    {
        return $this->createConfigurationFromArray($this->symfonyConfigFactory->createDefault());
    }

    /**
     * Converts the phpDocumentor configuration xml to an array.
     *
     * @param UriInterface $uri The location of the file to be loaded.
     *
     * @throws InvalidConfigPathException If $uri points to an inexistent file.
     */
    public function fromUri(UriInterface $uri): Configuration
    {
        $filename = (string) $uri;

        if (! file_exists($filename)) {
            throw new InvalidConfigPathException(sprintf('File %s could not be found', $filename));
        }

        $config = $this->symfonyConfigFactory->createFromFile($filename);

        return $this->applyMiddleware($this->createConfigurationFromArray($config), $uri);
    }

    public function fromDefault(): Configuration
    {
        return $this->applyMiddleware($this->createDefault(), null);
    }

    /**
     * Applies all middleware callbacks onto the configuration.
     */
    private function applyMiddleware(Configuration $configuration, UriInterface|null $uri): Configuration
    {
        foreach ($this->middlewares as $middleware) {
            $configuration = $middleware($configuration, $uri);
        }

        return $configuration;
    }

    /** @param ConfigurationMap $configuration */
    private function createConfigurationFromArray(array $configuration): Configuration
    {
        if (isset($configuration['phpdocumentor']['versions'])) {
            foreach ($configuration['phpdocumentor']['versions'] as $versionNumber => $version) {
                $configuration['phpdocumentor']['versions'][$versionNumber] = new VersionSpecification(
                    (string) $versionNumber,
                    array_map(
                        static fn ($api): ApiSpecification => ApiSpecification::createFromArray($api),
                        $version['api'],
                    ),
                    array_map(
                        static fn ($guide): GuideSpecification => new GuideSpecification(
                            new Source(
                                $guide['source']['dsn'],
                                $guide['source']['paths'],
                            ),
                            ltrim($guide['output'], '/'),
                            $guide['format'],
                        ),
                        $version['guides'],
                    ),
                );
            }
        }

        return new Configuration($configuration);
    }
}
