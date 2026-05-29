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

namespace phpDocumentor\Cache;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\Interfaces\ApiDocumentationSet;
use phpDocumentor\Descriptor\Interfaces\ProjectInterface;
use phpDocumentor\Descriptor\Interfaces\VersionInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\File;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

use function array_diff;
use function array_map;
use function md5;
use function sprintf;

/**
 * Maps a projectDescriptor to and from a cache instance.
 */
class ProjectDescriptorMapper
{
    final public const FILE_PREFIX = 'phpDocumentor-projectDescriptor-files-';

    final public const FILE_LIST = 'phpDocumentor-projectDescriptor-filelist';

    final public const KEY_SETTINGS = 'phpDocumentor-projectDescriptor-settings';

    /**
     * Initializes this mapper with the given cache instance.
     */
    public function __construct(private readonly CacheItemPoolInterface $cache)
    {
    }

    /**
     * Returns the Project Descriptor from the cache.
     */
    public function populate(ProjectInterface $projectDescriptor): void
    {
        $this->loadCacheItemAsSettings($projectDescriptor);

        foreach ($projectDescriptor->getVersions() as $version) {
            $this->populateVersion($version);
        }
    }

    /**
     * Stores a Project Descriptor in the Cache.
     */
    public function save(ProjectInterface $projectDescriptor): void
    {
        // store the settings for this Project Descriptor
        $item = $this->cache->getItem(self::KEY_SETTINGS);
        $this->cache->saveDeferred($item->set($projectDescriptor->getSettings()));

        foreach ($projectDescriptor->getVersions() as $version) {
            $this->saveVersion($version);
        }
    }

    /**
     * Removes all files in cache that do not occur in the given FileSet Collection.
     *
     * @param File[] $files
     */
    public function garbageCollect(VersionInterface $version, ApiDocumentationSet $apiSet, array $files): void
    {
        $fileListKey = $this->getApiSetFileListCacheKey($version, $apiSet);
        $fileListItem = $this->cache->getItem($fileListKey);
        $cachedFileList = $fileListItem->get();

        if ($cachedFileList === null) {
            return;
        }

        $realFileKeys = array_map(
            fn (File $file) => $this->getApiSetFileKey($fileListKey, $file->path()),
            $files,
        );

        $this->cache->deleteItems(array_diff($cachedFileList, $realFileKeys));
    }

    private function loadCacheItemAsSettings(ProjectInterface $projectDescriptor): void
    {
        $item = $this->cache->getItem(self::KEY_SETTINGS);
        if (! $item->isHit()) {
            return;
        }

        $settings = $item->get();

        if ($projectDescriptor instanceof ProjectDescriptor === false) {
            return;
        }

        $projectDescriptor->setSettings($settings);
    }

    private function populateVersion(VersionInterface $version): void
    {
        /** @var ApiDocumentationSet[] $apiSets */
        $apiSets = $version->getDocumentationSets()->filter(ApiDocumentationSet::class);

        foreach ($apiSets as $apiSet) {
            $this->populateApiSet($version, $apiSet);
        }
    }

    private function populateApiSet(VersionInterface $version, ApiDocumentationSet $apiSet): void
    {
        $key = $this->getApiSetFileListCacheKey($version, $apiSet);
        $fileList = $this->cache->getItem($key)->get();
        if ($fileList === null) {
            return;
        }

        /** @var CacheItemInterface $item */
        foreach ($this->cache->getItems($fileList) as $item) {
            $file = $item->get();

            if (! ($file instanceof FileDescriptor)) {
                continue;
            }

            $apiSet->getFiles()->set($file->getPath(), $file);
        }
    }

    private function saveVersion(VersionInterface $version): void
    {
        $apiSets = $version->getDocumentationSets()->filter(ApiDocumentationSet::class);
        foreach ($apiSets as $apiSet) {
            $this->saveApiSet($version, $apiSet);
        }
    }

    private function saveApiSet(VersionInterface $version, ApiDocumentationSet $apiSet): void
    {
        $fileListKey = $this->getApiSetFileListCacheKey($version, $apiSet);
        $fileListItem = $this->cache->getItem($fileListKey);
        $currentFileList = $fileListItem->get();

        // store cache items
        $fileKeys = [];
        foreach ($apiSet->getFiles() as $file) {
            $key = $this->getApiSetFileKey($fileListKey, $file->getPath());
            $fileKeys[] = $key;
            $item = $this->cache->getItem($key);
            $this->cache->saveDeferred($item->set($file));
        }

        $this->cache->saveDeferred($fileListItem->set($fileKeys));
        $this->cache->commit();

        if ($currentFileList === null) {
            return;
        }

        // remove any keys that are no longer used.
        $invalidatedKeys = array_diff($currentFileList, $fileKeys);
        if (! $invalidatedKeys) {
            return;
        }

        $this->cache->deleteItems($invalidatedKeys);
    }

    private function getApiSetFileListCacheKey(VersionInterface $version, ApiDocumentationSet $apiSet): string
    {
        return sprintf('%s-%s-%s', self::FILE_LIST, $version->getNumber(), $apiSet->getName());
    }

    private function getApiSetFileKey(string $fileListKey, string $path): string
    {
        return sprintf('%s%s', self::FILE_PREFIX, md5($fileListKey . $path));
    }
}
