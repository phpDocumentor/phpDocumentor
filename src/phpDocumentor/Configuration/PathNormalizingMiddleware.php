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
use function ltrim;
use function rtrim;
use function strpos;
use function substr;

final class PathNormalizingMiddleware implements MiddlewareInterface
{
    /**
     * @param array<string, array<string, array<string, mixed>>> $configuration
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function __invoke(array $configuration, ?UriInterface $uri = null) : array
    {
        $configuration = $this->makeDsnRelativeToConfig($configuration, $uri);

        $configuration['phpdocumentor']['paths']['cache']
            = $this->normalizeCachePath($uri, $configuration['phpdocumentor']['paths']['cache']);

        $configuration['phpdocumentor'] = $this->normalizePaths($configuration['phpdocumentor']);

        return $configuration;
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
     *
     * @param array<string, array<string, array<string, mixed>>> $configuration
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function makeDsnRelativeToConfig(array $configuration, ?UriInterface $uri) : array
    {
        if ($uri === null) {
            return $configuration;
        }

        $configFile = Dsn::createFromUri($uri);
        $configPath = $configFile->withPath(Path::dirname($configFile->getPath()));

        $configuration['phpdocumentor']['paths']['output'] =
            $configuration['phpdocumentor']['paths']['output']->resolve($configPath);
        foreach ($configuration['phpdocumentor']['versions'] as $versionNumber => $version) {
            foreach ($version['api'] as $key => $api) {
                $configuration['phpdocumentor']['versions'][$versionNumber]['api'][$key]['source']['dsn']
                    = $api['source']['dsn']->resolve($configPath);
            }

            foreach ($version['guides'] as $key => $guide) {
                $configuration['phpdocumentor']['versions'][$versionNumber]['guides'][$key]['source']['dsn']
                    = $guide['source']['dsn']->resolve($configPath);
            }
        }

        return $configuration;
    }

    /**
     * @param array<string, array<string, array<string, mixed>>> $configuration
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function normalizePaths(array $configuration) : array
    {
        foreach ($configuration['versions'] as $versionNumber => $version) {
            foreach ($version['api'] as $key => $api) {
                foreach ($api['source']['paths'] as $subkey => $path) {
                    $configuration['versions'][$versionNumber]['api'][$key]['source']['paths'][$subkey] =
                        $this->pathToGlobPattern((string) $path);
                }

                foreach ($api['ignore']['paths'] as $subkey => $path) {
                    $configuration['versions'][$versionNumber]['api'][$key]['ignore']['paths'][$subkey] =
                        $this->pathToGlobPattern((string) $path);
                }
            }

            foreach ($version['guides'] as $key => $guide) {
                foreach ($guide['source']['paths'] as $subkey => $path) {
                    $configuration['versions'][$versionNumber]['guides'][$key]['source']['paths'][$subkey] =
                        $this->normalizePath((string) $path);
                }
            }
        }

        return $configuration;
    }

    private function normalizePath(string $path) : string
    {
        if (strpos($path, '.') === 0) {
            $path = ltrim($path, '.');
        }

        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        return rtrim($path, '/');
    }

    private function pathToGlobPattern(string $path) : string
    {
        $path = $this->normalizePath($path);

        if (substr($path, -1) !== '*' && strpos($path, '.') === false) {
            $path .= '/**/*';
        }

        return $path;
    }

    public function normalizeCachePath(?UriInterface $uri, Path $cachePath) : Path
    {
        if ($cachePath::isAbsolutePath((string) $cachePath)) {
            return $cachePath;
        }

        if ($uri === null) {
            return $cachePath;
        }

        $configFile = Dsn::createFromUri($uri);
        $configPath = $configFile->withPath(Path::dirname($configFile->getPath()));

        return Dsn::createFromString((string) $cachePath)->resolve($configPath)->getPath();
    }
}
