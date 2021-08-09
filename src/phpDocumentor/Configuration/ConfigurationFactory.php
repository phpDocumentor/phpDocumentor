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
use phpDocumentor\Dsn;
use phpDocumentor\UriFactory;

use function array_map;
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
            } catch (InvalidConfigPathException $e) {
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

        if (!file_exists($filename)) {
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
    private function applyMiddleware(Configuration $configuration, ?UriInterface $uri): Configuration
    {
        foreach ($this->middlewares as $middleware) {
            $configuration = $middleware($configuration, $uri);
        }

        return $configuration;
    }

    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @param array{phpdocumentor: array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, array{ api: array{ignore-tags: array<string>, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibility: non-empty-array<string>, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}, encoding: string, output: string, default-package-name: string, examples: array{dsn: Dsn, paths: array}, include-source: bool, validate: bool}, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>}} $configuration
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    private function createConfigurationFromArray(array $configuration): Configuration
    {
        if (isset($configuration['phpdocumentor']['versions'])) {
            foreach ($configuration['phpdocumentor']['versions'] as $versionNumber => $version) {
                $configuration['phpdocumentor']['versions'][$versionNumber] = new VersionSpecification(
                    $versionNumber,
                    array_map(
                        static function ($api): ApiSpecification {
                            return ApiSpecification::createFromArray($api);
                        },
                        $version['api']
                    ),
                    array_map(
                        static function ($guide): GuideSpecification {
                            return new GuideSpecification(
                                new Source(
                                    $guide['source']['dsn'],
                                    $guide['source']['paths']
                                ),
                                $guide['output'],
                                $guide['format']
                            );
                        },
                        $version['guides']
                    )
                );
            }
        }

        return new Configuration($configuration);
    }
}
