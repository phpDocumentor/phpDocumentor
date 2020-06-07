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

namespace phpDocumentor\Descriptor\Cache;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\File;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use function array_diff;
use function array_map;
use function md5;

/**
 * Maps a projectDescriptor to and from a cache instance.
 */
class ProjectDescriptorMapper
{
    public const FILE_PREFIX = 'phpDocumentor-projectDescriptor-files-';

    public const FILE_LIST = 'phpDocumentor-projectDescriptor-filelist';

    public const KEY_SETTINGS = 'phpDocumentor-projectDescriptor-settings';

    /** @var AdapterInterface $cache */
    private $cache;

    /**
     * Initializes this mapper with the given cache instance.
     */
    public function __construct(AdapterInterface $descriptors)
    {
        $this->cache = $descriptors;
    }

    /**
     * Returns the Project Descriptor from the cache.
     */
    public function populate(ProjectDescriptor $projectDescriptor) : void
    {
        $this->loadCacheItemAsSettings($projectDescriptor);

        $fileList = $this->cache->getItem(self::FILE_LIST)->get();
        if ($fileList === null) {
            return;
        }

        /** @var CacheItemInterface $item */
        foreach ($this->cache->getItems($fileList) as $item) {
            $file = $item->get();

            if (!($file instanceof FileDescriptor)) {
                continue;
            }

            $projectDescriptor->getFiles()->set($file->getPath(), $file);
        }
    }

    /**
     * Stores a Project Descriptor in the Cache.
     */
    public function save(ProjectDescriptor $projectDescriptor) : void
    {
        $fileListItem    = $this->cache->getItem(self::FILE_LIST);
        $currentFileList = $fileListItem->get();

        // store the settings for this Project Descriptor
        $item = $this->cache->getItem(self::KEY_SETTINGS);
        $this->cache->saveDeferred($item->set($projectDescriptor->getSettings()));

        // store cache items
        $fileKeys = [];
        foreach ($projectDescriptor->getFiles() as $file) {
            $key        = self::FILE_PREFIX . md5($file->getPath());
            $fileKeys[] = $key;
            $item       = $this->cache->getItem($key);
            $this->cache->saveDeferred($item->set($file));
        }

        $this->cache->saveDeferred($fileListItem->set($fileKeys));
        $this->cache->commit();

        if ($currentFileList === null) {
            return;
        }

        // remove any keys that are no longer used.
        $invalidatedKeys = array_diff($currentFileList, $fileKeys);
        if (!$invalidatedKeys) {
            return;
        }

        $this->cache->deleteItems($invalidatedKeys);
    }

    /**
     * Removes all files in cache that do not occur in the given FileSet Collection.
     *
     * @param File[] $files
     */
    public function garbageCollect(array $files) : void
    {
        $fileListItem   = $this->cache->getItem(self::FILE_LIST);
        $cachedFileList = $fileListItem->get();

        if ($cachedFileList === null) {
            return;
        }

        $realFileKeys = array_map(
            static function (File $file) {
                return self::FILE_PREFIX . md5($file->path());
            },
            $files
        );

        $this->cache->deleteItems(array_diff($cachedFileList, $realFileKeys));
    }

    private function loadCacheItemAsSettings(ProjectDescriptor $projectDescriptor) : void
    {
        $item = $this->cache->getItem(self::KEY_SETTINGS);
        if (!$item->isHit()) {
            return;
        }

        $settings = $item->get();
        $projectDescriptor->setSettings($settings);
    }
}
