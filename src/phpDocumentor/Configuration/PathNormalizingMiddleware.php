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
use phpDocumentor\Dsn;
use phpDocumentor\Path;
use Symfony\Component\Filesystem\Path as SymfonyPath;

use function array_map;
use function array_merge;
use function ltrim;
use function rtrim;
use function str_replace;
use function strpos;
use function substr;

final class PathNormalizingMiddleware implements MiddlewareInterface
{
    public function __invoke(Configuration $configuration, ?UriInterface $uri = null): Configuration
    {
        $configuration = $this->makeDsnRelativeToConfig($configuration, $uri);

        $configuration['phpdocumentor']['paths']['cache']
            = $this->normalizeCachePath($uri, $configuration['phpdocumentor']['paths']['cache']);

        return $this->normalizePaths($configuration);
    }

    /**
     * Transforms relative dsn to relative path of working dir.
     *
     * The dsn defined in the config might be relative. If it is relative it is relative to the location
     * of the config file. The config file could be outside the working directory. But we want to read from the current
     * working dir.
     *
     * Eg. the config is read from './config/phpdoc.xml'
     * The defined source is '../src'
     *
     * In this case the src dir on the same level as the config dir is read.
     *
     * Absolute DSNs are untouched.
     */
    private function makeDsnRelativeToConfig(Configuration $configuration, ?UriInterface $uri): Configuration
    {
        if ($uri === null) {
            return $configuration;
        }

        $configFile = Dsn::createFromUri($uri);
        $configPath = Path::dirname($configFile->getPath());
        $configDsn = $configFile->withPath($configPath);

        $configuration['phpdocumentor']['paths']['output'] =
            $configuration['phpdocumentor']['paths']['output']->resolve($configDsn);

        /** @var VersionSpecification $version */
        foreach ($configuration['phpdocumentor']['versions'] as $version) {
            $apiConfigs = [];

            foreach ($version->getApi() as $api) {
                $apiConfigs[] = $api->withSource($api->source()->withDsn($api['source']['dsn']->resolve($configDsn)));
            }

            $version->setApi($apiConfigs);

            foreach ($version->getGuides() ?? [] as $key => $guide) {
                $version->guides[$key]->withSource(
                    $guide->source()->withDsn(
                        $guide->source()->dsn()->resolve($configDsn)
                    )
                );
            }
        }

        /** @var array{name: string, location?: ?Path, parameters?: array<string, mixed>} $template */
        foreach ($configuration['phpdocumentor']['templates'] as $key => $template) {
            $location = $template['location'];
            if ($location instanceof Path && SymfonyPath::isAbsolute((string) $location) === false) {
                $location = new Path($configPath . '/' . $location);
            }

            $template['location'] = $location;

            $configuration['phpdocumentor']['templates'][$key] = $template;
        }

        return $configuration;
    }

    private function normalizePaths(Configuration $configuration): Configuration
    {
        /** @var VersionSpecification $version */
        foreach ($configuration['phpdocumentor']['versions'] as $version) {
            foreach ($version->getApi() as $key => $api) {
                $api->setIgnore(
                    array_merge(
                        $api['ignore'],
                        [
                            'paths' => array_map(
                                function (string $path): string {
                                    return $this->pathToGlobPattern($path);
                                },
                                $api['ignore']['paths']
                            ),
                        ]
                    )
                );

                $version->api[$key] = $api;
            }

            foreach ($version->getGuides() ?? [] as $key => $guide) {
                $version->guides[$key] = $guide->withSource(
                    $guide->source()->withPaths(
                        array_map(
                            function (string $path): Path {
                                return new Path($this->normalizePath((string) $path));
                            },
                            $guide->source()->paths()
                        )
                    )
                );
            }
        }

        return $configuration;
    }

    private function normalizePath(string $path): string
    {
        if (strpos($path, '.') === 0) {
            $path = ltrim($path, '.');
        }

        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        $path = rtrim($path, '/');

        if ($path === '') {
            return '.';
        }

        return $path;
    }

    private function pathToGlobPattern(string $path): string
    {
        $path = $this->normalizePath($path);

        if ($path === '.') {
            return '/**/*';
        }

        if (substr($path, -1) !== '*' && strpos($path, '.') === false) {
            $path .= '/**/*';
        }

        return $path;
    }

    public function normalizeCachePath(?UriInterface $uri, Path $cachePath): Path
    {
        $cachePathAsString = $cachePath->decoded();
        if ($cachePath::isAbsolutePath($cachePathAsString)) {
            return $cachePath;
        }

        if ($uri === null) {
            return $cachePath;
        }

        $configFile = Dsn::createFromUri($uri);
        $configPath = $configFile->withPath(Path::dirname($configFile->getPath()));

        return Dsn::createFromString($cachePathAsString)->resolve($configPath)->getPath();
    }
}
