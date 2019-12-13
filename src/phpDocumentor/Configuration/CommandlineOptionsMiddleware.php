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

use phpDocumentor\Dsn;
use phpDocumentor\Path;
use function array_map;
use function array_merge;
use function array_unique;
use function count;
use function current;
use function explode;
use function is_array;

final class CommandlineOptionsMiddleware
{
    /** @var string[] */
    private $options = [];

    /** @var ConfigurationFactory */
    private $configFactory;

    public function __construct(array $options, ConfigurationFactory $configFactory)
    {
        $this->options = $options;
        $this->configFactory = $configFactory;
    }

    public function __invoke(array $configuration) : array
    {
        $configuration = $this->overwriteDestinationFolder($configuration);
        $configuration = $this->disableCache($configuration);
        $configuration = $this->overwriteCacheFolder($configuration);
        $configuration = $this->overwriteTitle($configuration);
        $configuration = $this->overwriteTemplates($configuration);

        if (!isset($configuration['phpdocumentor']['versions'])) {
            return $configuration;
        }

        foreach ($configuration['phpdocumentor']['versions'] as &$version) {
            $version = $this->setFilesInPath($version);
            $version = $this->setDirectoriesInPath($version);
            $version = $this->registerExtensions($version);
            $version = $this->overwriteIgnoredPaths($version);
            $version = $this->overwriteMarkers($version);
            $version = $this->overwriteIncludeSource($version);
            $version = $this->overwriteVisibility($version);
            $version = $this->overwriteDefaultPackageName($version);
        }

        return $configuration;
    }

    private function overwriteDestinationFolder(array $configuration) : array
    {
        if (isset($this->options['target']) && $this->options['target']) {
            $configuration['phpdocumentor']['paths']['output'] = new Dsn($this->options['target']);
        }

        return $configuration;
    }

    /**
     * Changes the given configuration array so that the cache handling is disabled.
     */
    private function disableCache(array $configuration) : array
    {
        if (isset($this->options['force']) && $this->options['force']) {
            $configuration['phpdocumentor']['use-cache'] = false;
        }

        return $configuration;
    }

    private function overwriteCacheFolder(array $configuration) : array
    {
        if (isset($this->options['cache-folder']) && $this->options['cache-folder']) {
            $configuration['phpdocumentor']['paths']['cache'] = new Path($this->options['cache-folder']);
        }

        return $configuration;
    }

    private function overwriteTitle(array $configuration) : array
    {
        if (isset($this->options['title']) && $this->options['title']) {
            $configuration['phpdocumentor']['title'] = $this->options['title'];
        }

        return $configuration;
    }

    /**
     * Changes the given configuration array to feature the templates from the options.
     */
    private function overwriteTemplates(array $configuration) : array
    {
        if (isset($this->options['template']) && $this->options['template']) {
            $configuration['phpdocumentor']['templates'] = array_map(
                static function ($templateName) {
                    return ['name' => $templateName];
                },
                (array) $this->options['template']
            );
        }

        return $configuration;
    }

    private function setFilesInPath(array $version) : array
    {
        $filename = $this->options['filename'] ?? null;
        if (!$filename) {
            return $version;
        }

        if (!isset($version['api'])) {
            $version['api'] = $this->createDefaultApiSettings();
        }

        $version['api'][0]['source']['paths'] = array_map(
            static function ($path) {
                return new Path($path);
            },
            $filename
        );

        return $version;
    }

    private function setDirectoriesInPath(array $version) : array
    {
        /** @var string|string[]|null $directory */
        $directory = $this->options['directory'] ?? null;
        if (!$directory) {
            return $version;
        }

        if (!is_array($directory)) {
            $directory = [$directory];
        }

        $currentApiConfig = current($this->createDefaultApiSettings());

        if (isset($version['api'])) {
            $currentApiConfig = current($version['api']);
        }

        //Reset the current config, because directory it overwriting the config.
        $currentApiConfig['source']['paths'] = [];
        $version['api'] = [];

        foreach ($directory as $path) {
            //If the passed directory is an absolute path this should be handled as a new Api
            //A version may contain multiple APIs.
            if (Path::isAbsolutePath($path)) {
                $apiConfig = $currentApiConfig;
                $apiConfig['source']['dsn'] = new Dsn($path);
                $apiConfig['source']['paths'] = [new Path('./')];
                $version['api'][] = $apiConfig;
            } else {
                $currentApiConfig['source']['paths'][] = new Path($path);
            }
        }

        if (count($currentApiConfig['source']['paths']) > 0) {
            $version['api'][] = $currentApiConfig;
        }

        return $version;
    }

    private function registerExtensions(array $version) : array
    {
        if (!isset($this->options['extensions']) || !$this->options['extensions']) {
            return $version;
        }

        if (!isset($version['api'])) {
            $version['api'] = $this->createDefaultApiSettings();
        }

        $version['api'][0]['extensions'] = $this->options['extensions'];

        return $version;
    }

    private function overwriteIgnoredPaths(array $version) : array
    {
        if (!isset($this->options['ignore']) || !$this->options['ignore']) {
            return $version;
        }

        if (!isset($version['api'])) {
            $version['api'] = $this->createDefaultApiSettings();
        }

        $version['api'][0]['ignore']['paths'] = array_map(
            static function ($path) {
                return new Path($path);
            },
            $this->options['ignore']
        );

        return $version;
    }

    private function overwriteMarkers(array $version) : array
    {
        if (!isset($this->options['markers']) || !$this->options['markers']) {
            return $version;
        }

        if (!isset($version['api'])) {
            $version['api'] = $this->createDefaultApiSettings();
        }

        $version['api'][0]['markers'] = $this->options['markers'];

        return $version;
    }

    private function overwriteIncludeSource(array $version) : array
    {
        if (!isset($this->options['sourcecode'])) {
            return $version;
        }

        if (!isset($version['api'])) {
            $version['api'] = $this->createDefaultApiSettings();
        }

        $version['api'][0]['include-source'] = $this->options['sourcecode'];

        return $version;
    }

    private function overwriteVisibility(array $version) : array
    {
        /** @var string[]|string|null $visibilityFlags */
        $visibilityFlags = $this->options['visibility'] ?? null;
        if (!$visibilityFlags) {
            return $version;
        }

        if (!is_array($visibilityFlags)) {
            $visibilityFlags = [$visibilityFlags];
        }

        if (!isset($version['api'])) {
            $version['api'] = $this->createDefaultApiSettings();
        }

        $visibilities = [];
        foreach ($visibilityFlags as $visibility) {
            $visibilities = array_merge($visibilities, explode(',', $visibility));
        }
        $visibilities = array_unique($visibilities);
        $version['api'][0]['visibility'] = $visibilities;

        return $version;
    }

    private function overwriteDefaultPackageName(array $version) : array
    {
        if (!isset($this->options['defaultpackagename']) || !$this->options['defaultpackagename']) {
            return $version;
        }

        if (!isset($version['api'])) {
            $version['api'] = $this->createDefaultApiSettings();
        }

        $version['api'][0]['default-package-name'] = $this->options['defaultpackagename'];

        return $version;
    }

    private function createDefaultApiSettings() : array
    {
        return current($this->configFactory->createDefault()['versions'])['api'];
    }
}
