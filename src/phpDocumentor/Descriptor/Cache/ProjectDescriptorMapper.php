<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Cache;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\File;
use Psr\Cache\CacheItemPoolInterface;
use Stash\Item;

/**
 * Maps a projectDescriptor to and from a cache instance.
 */
final class ProjectDescriptorMapper
{
    const FILE_PREFIX = 'phpDocumentor/projectDescriptor/files/';

    const FILE_LIST = 'phpDocumentor/projectDescriptor/filelist';

    const KEY_SETTINGS = 'phpDocumentor/projectDescriptor/settings';

    /** @var CacheItemPoolInterface */
    private $cache;

    /**
     * Initializes this mapper with the given cache instance.
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Returns the Project Descriptor from the cache.
     */
    public function populate(ProjectDescriptor $projectDescriptor): void
    {
        $this->loadCacheItemAsSettings($projectDescriptor);

        $fileList = $this->cache->getItem(self::FILE_LIST)->get();
        if ($fileList !== null) {
            /** @var Item $item */
            foreach ($this->cache->getItems($fileList) as $item) {
                $file = $item->get();

                if ($file instanceof FileDescriptor) {
                    $projectDescriptor->getFiles()->set($file->getPath(), $file);
                }
            }
        }
    }

    /**
     * Stores a Project Descriptor in the Cache.
     */
    public function save(ProjectDescriptor $projectDescriptor): void
    {
        $fileListItem = $this->cache->getItem(self::FILE_LIST);
        $currentFileList = $fileListItem->get();

        // store the settings for this Project Descriptor
        $item = $this->cache->getItem(self::KEY_SETTINGS);
        $this->cache->saveDeferred($item->set($projectDescriptor->getSettings()));

        // store cache items
        $fileKeys = [];
        foreach ($projectDescriptor->getFiles() as $file) {
            $key = self::FILE_PREFIX . md5($file->getPath());
            $fileKeys[] = $key;
            $item = $this->cache->getItem($key);
            $this->cache->saveDeferred($item->set($file));
        }

        $this->cache->saveDeferred($fileListItem->set($fileKeys));
        $this->cache->commit();

        if ($currentFileList !== null) {
            // remove any keys that are no longer used.
            $invalidatedKeys = array_diff($currentFileList, $fileKeys);
            if ($invalidatedKeys) {
                $this->cache->deleteItems($invalidatedKeys);
            }
        }
    }

    /**
     * Removes all files in cache that do not occur in the given FileSet Collection.
     *
     * @param File[] $files
     */
    public function garbageCollect(array $files) : void
    {
        $fileListItem = $this->cache->getItem(self::FILE_LIST);
        $cachedFileList = $fileListItem->get();

        if ($cachedFileList !== null) {
            $realFileKeys = array_map(
                static function (File $file) {
                    return self::FILE_PREFIX . md5($file->path());
                },
                $files
            );

            $this->cache->deleteItems(array_diff($cachedFileList, $realFileKeys));
        }
    }

    private function loadCacheItemAsSettings(ProjectDescriptor $projectDescriptor): void
    {
        $item = $this->cache->getItem(self::KEY_SETTINGS);
        if ($item->isHit()) {
            $settings = $item->get();
            $projectDescriptor->setSettings($settings);
        }
    }
}
