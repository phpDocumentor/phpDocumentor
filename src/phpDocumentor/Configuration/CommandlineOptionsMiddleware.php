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
use Webmozart\Assert\Assert;

use function array_map;
use function array_merge;
use function array_unique;
use function count;
use function current;
use function end;
use function explode;
use function implode;
use function is_countable;

use const DIRECTORY_SEPARATOR;

final class CommandlineOptionsMiddleware implements MiddlewareInterface
{
    private readonly Dsn $currentWorkingDir;

    /** @param array<string|string[]> $options */
    public function __construct(
        private readonly array $options,
        private readonly ConfigurationFactory $configFactory,
        string $currentWorkingDir,
    ) {
        $this->currentWorkingDir = Dsn::createFromString($currentWorkingDir);
    }

    public function __invoke(Configuration $configuration, UriInterface|null $uri = null): Configuration
    {
        $configuration = $this->overwriteDestinationFolder($configuration);
        $configuration = $this->disableCache($configuration);
        $configuration = $this->overwriteCacheFolder($configuration);
        $configuration = $this->overwriteTitle($configuration);
        $configuration = $this->overwriteTemplates($configuration);
        $configuration = $this->overwriteSettings($configuration);

        if (! isset($configuration['phpdocumentor']['versions'])) {
            $configuration['phpdocumentor']['versions']['1.0.0'] = $this->createDefaultVersionSettings();
        }

        if ($this->shouldReduceNumberOfVersionsToOne($configuration)) {
            $configuration['phpdocumentor']['versions'] = [
                '1.0.0' => end($configuration['phpdocumentor']['versions']),
            ];
        }

        foreach ($configuration['phpdocumentor']['versions'] as &$version) {
            $version = $this->setDirectoriesInPath($version);
            $version = $this->setFilesInPath($version);
            $version = $this->registerExtensions($version);
            $version = $this->overwriteIgnoredPaths($version);
            $version = $this->overwriteIgnoredSymlinks($version);
            $version = $this->overwriteIgnoredTags($version);
            $version = $this->overwriteMarkers($version);
            $version = $this->overwriteIncludeSource($version);
            $version = $this->overwriteVisibility($version);
            $version = $this->overwriteExamples($version);
            $version = $this->overwriteEncoding($version);
            $version = $this->overwriteDefaultPackageName($version);
        }

        return $configuration;
    }

    private function overwriteDestinationFolder(Configuration $configuration): Configuration
    {
        if (isset($this->options['target']) && $this->options['target']) {
            $configuration['phpdocumentor']['paths']['output'] = Dsn::createFromString($this->options['target'])
                ->resolve($this->currentWorkingDir);
        }

        return $configuration;
    }

    private function disableCache(Configuration $configuration): Configuration
    {
        if (isset($this->options['force']) && $this->options['force']) {
            $configuration['phpdocumentor']['use_cache'] = false;
        }

        return $configuration;
    }

    private function overwriteCacheFolder(Configuration $configuration): Configuration
    {
        $cacheFolder = $this->options['cache-folder'] ?? null;
        if ($cacheFolder !== null) {
            if (SymfonyPath::isAbsolute($cacheFolder) === false) {
                $cacheFolder = $this->currentWorkingDir->getPath() . DIRECTORY_SEPARATOR . $cacheFolder;
            }

            $configuration['phpdocumentor']['paths']['cache'] = new Path($cacheFolder);
        }

        return $configuration;
    }

    private function overwriteTitle(Configuration $configuration): Configuration
    {
        if (isset($this->options['title']) && $this->options['title']) {
            $configuration['phpdocumentor']['title'] = $this->options['title'];
        }

        return $configuration;
    }

    private function overwriteTemplates(Configuration $configuration): Configuration
    {
        if (isset($this->options['template']) && $this->options['template']) {
            $configuration['phpdocumentor']['templates'] = array_map(
                static fn ($templateName) => ['name' => $templateName],
                (array) $this->options['template'],
            );
        }

        return $configuration;
    }

    private function setFilesInPath(VersionSpecification $version): VersionSpecification
    {
        $filename = $this->options['filename'] ?? null;
        if (! $filename) {
            return $version;
        }

        $filename = explode(',', implode(',', $filename));

        if (empty($version->getApi())) {
            $version->addApi($this->createDefaultApiSettings());
        }

        $version->api[0] = $version->api[0]->withSource(
            $version->api[0]->source()->withPaths(
                array_map(
                    static fn ($path): Path => new Path($path),
                    $filename,
                ),
            ),
        );

        return $version;
    }

    private function setDirectoriesInPath(VersionSpecification $version): VersionSpecification
    {
        /** @var string|string[]|null $directory */
        $directory = $this->options['directory'] ?? '';
        if (! $directory) {
            return $version;
        }

        // this will ensure that if we receive an array with comma-separated values; that we make a single array with
        // all values split
        $directory = explode(',', implode(',', $directory));

        $currentApiConfig = current($version->getApi());
        if (! $currentApiConfig) {
            $currentApiConfig = $this->createDefaultApiSettings();
        }

        // Reset the current config, because directory is overwriting the config.
        $currentApiConfig = $currentApiConfig->withSource(new Source($currentApiConfig['source']['dsn'], []));

        $version->setApi([]);
        foreach ($directory as $path) {
            // If the passed directory is an absolute path this should be handled as a new Api
            // A version may contain multiple APIs.
            if (Path::isAbsolutePath($path)) {
                $version->addApi(
                    $currentApiConfig->withSource(new Source(Dsn::createFromString($path), [new Path('./')])),
                );
            } else {
                $currentApiConfig = $currentApiConfig->withSource(
                    $currentApiConfig->source()->withPaths(
                        array_merge($currentApiConfig->source()->paths(), [new Path($path)]),
                    ),
                );
            }
        }

        if (count($currentApiConfig->source()->paths()) > 0) {
            $version->addApi($currentApiConfig);
        }

        return $version;
    }

    private function registerExtensions(VersionSpecification $version): VersionSpecification
    {
        if (! isset($this->options['extensions']) || ! $this->options['extensions']) {
            return $version;
        }

        if (empty($version->getApi())) {
            $version->addApi($this->createDefaultApiSettings());
        }

        Assert::isArray($this->options['extensions']);

        $version->api[0]['extensions'] = $this->options['extensions'];

        return $version;
    }

    private function overwriteIgnoredPaths(VersionSpecification $version): VersionSpecification
    {
        if (! isset($this->options['ignore']) || ! $this->options['ignore']) {
            return $version;
        }

        if (empty($version->getApi())) {
            $version->addApi($this->createDefaultApiSettings());
        }

        $version->api[0]->setIgnore(
            array_merge(
                $version->api[0]['ignore'],
                [
                    'paths' => array_map(
                        static fn ($path): Path => new Path($path),
                        $this->options['ignore'],
                    ),
                ],
            ),
        );

        return $version;
    }

    private function overwriteIgnoredTags(VersionSpecification $version): VersionSpecification
    {
        if (! isset($this->options['ignore-tags']) || ! $this->options['ignore-tags']) {
            return $version;
        }

        if (empty($version->getApi())) {
            $version->addApi($this->createDefaultApiSettings());
        }

        Assert::isArray($this->options['ignore-tags']);

        $version->api[0]['ignore-tags'] = $this->options['ignore-tags'];

        return $version;
    }

    private function overwriteMarkers(VersionSpecification $version): VersionSpecification
    {
        if (! isset($this->options['markers']) || ! $this->options['markers']) {
            return $version;
        }

        if (empty($version->getApi())) {
            $version->addApi($this->createDefaultApiSettings());
        }

        Assert::isArray($this->options['markers']);

        $version->api[0]['markers'] = $this->options['markers'];

        return $version;
    }

    private function overwriteIncludeSource(VersionSpecification $version): VersionSpecification
    {
        if (! isset($this->options['sourcecode'])) {
            return $version;
        }

        if (empty($version->getApi())) {
            $version->addApi($this->createDefaultApiSettings());
        }

        $version->api[0]['include-source'] = $this->options['sourcecode'];

        return $version;
    }

    private function overwriteVisibility(VersionSpecification $version): VersionSpecification
    {
        /** @var string[]|string|null $visibilityFlags */
        $visibilityFlags = $this->options['visibility'] ?? null;
        if (! $visibilityFlags) {
            return $version;
        }

        if (empty($version->getApi())) {
            $version->addApi($this->createDefaultApiSettings());
        }

        $visibilities = array_unique(explode(',', implode(',', $visibilityFlags)));
        $version->api[0]['visibility'] = $visibilities;

        return $version;
    }

    private function overwriteDefaultPackageName(VersionSpecification $version): VersionSpecification
    {
        if (! isset($this->options['defaultpackagename']) || ! $this->options['defaultpackagename']) {
            return $version;
        }

        if (empty($version->getApi())) {
            $version->addApi($this->createDefaultApiSettings());
        }

        $version->api[0]['default-package-name'] = $this->options['defaultpackagename'];

        return $version;
    }

    private function overwriteExamples(VersionSpecification $version): VersionSpecification
    {
        /** @var string|null $examples */
        $examples = $this->options['examples-dir'] ?? null;
        if (! $examples) {
            return $version;
        }

        if (empty($version->getApi())) {
            $version->addApi($this->createDefaultApiSettings());
        }

        $version->api[0]['examples'] = new Source(Dsn::createFromString($examples), [new Path('./')]);

        return $version;
    }

    private function createDefaultVersionSettings(): VersionSpecification
    {
        return current($this->configFactory->createDefault()['phpdocumentor']['versions']);
    }

    private function createDefaultApiSettings(): ApiSpecification
    {
        return current($this->createDefaultVersionSettings()->getApi());
    }

    /**
     * If the source path was influenced; we can no longer reliable render multiple versions as such we reduce
     * the list of versions to the last one; assuming that is the most recent / desirable one.
     */
    private function shouldReduceNumberOfVersionsToOne(Configuration $configuration): bool
    {
        return (($this->options['filename'] ?? '') !== '' || ($this->options['directory'] ?? '') !== '')
            && (is_countable($configuration['phpdocumentor']['versions'])
                ? count($configuration['phpdocumentor']['versions']) : 0) > 1;
    }

    private function overwriteSettings(Configuration $configuration): Configuration
    {
        if (! ($configuration['phpdocumentor']['settings'] ?? null)) {
            $configuration['phpdocumentor']['settings'] = [];
        }

        foreach (($this->options['setting'] ?? []) as $setting) {
            [$key, $value] = explode('=', $setting);

            if (! $key || ! $value) {
                continue;
            }

            if ($value === 'on' || $value === 'true') {
                $value = true;
            }

            if ($value === 'off' || $value === 'false') {
                $value = false;
            }

            $configuration['phpdocumentor']['settings'][$key] = $value;
        }

        return $configuration;
    }

    private function overwriteEncoding(VersionSpecification $version): VersionSpecification
    {
        /** @var string|null $encoding */
        $encoding = $this->options['encoding'] ?? null;
        if (! $encoding) {
            return $version;
        }

        if (empty($version->getApi())) {
            $version->addApi($this->createDefaultApiSettings());
        }

        $version->api[0]['encoding'] = $encoding;

        return $version;
    }

    private function overwriteIgnoredSymlinks(VersionSpecification $version): VersionSpecification
    {
        if (isset($this->options['ignore-symlinks']) === false) {
            return $version;
        }

        $version->api[0]->setIgnore(
            array_merge(
                $version->api[0]['ignore'],
                [
                    'symlinks' => $this->options['ignore-symlinks'],
                ],
            ),
        );

        return $version;
    }
}
