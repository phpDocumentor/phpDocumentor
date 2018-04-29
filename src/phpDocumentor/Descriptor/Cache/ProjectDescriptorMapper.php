<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Cache;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Fileset\Collection;
use Zend\Cache\Storage\IterableInterface;
use Zend\Cache\Storage\OptimizableInterface;
use Zend\Cache\Storage\StorageInterface;

/**
 * Maps a projectDescriptor to and from a cache instance.
 */
final class ProjectDescriptorMapper
{
    const FILE_PREFIX = 'file_';

    const KEY_SETTINGS = 'settings';

    /** @var StorageInterface|IterableInterface */
    private $cache;

    /**
     * Initializes this mapper with the given cache instance.
     */
    public function __construct(StorageInterface $cache)
    {
        if (!$cache instanceof IterableInterface) {
            throw new \InvalidArgumentException('ProjectDescriptorMapper should also be an iterable Storage type');
        }

        $this->cache = $cache;
    }

    /**
     * Returns the Cache instance for this Mapper.
     */
    public function getCache(): StorageInterface
    {
        return $this->cache;
    }

    /**
     * Returns the Project Descriptor from the cache.
     */
    public function populate(ProjectDescriptor $projectDescriptor)
    {
        $this->loadCacheItemAsSettings($projectDescriptor, self::KEY_SETTINGS);

        foreach ($this->getCache() as $key) {
            $this->loadCacheItemAsFile($projectDescriptor, $key);
        }
    }

    /**
     * Stores a Project Descriptor in the Cache.
     */
    public function save(ProjectDescriptor $projectDescriptor)
    {
        $keys = [];
        $cache = $this->getCache();

        foreach ($cache as $key) {
            $keys[] = $key;
        }

        // store the settings for this Project Descriptor
        $cache->setItem(self::KEY_SETTINGS, $projectDescriptor->getSettings());

        // store cache items
        $usedKeys = [self::KEY_SETTINGS];
        foreach ($projectDescriptor->getFiles() as $file) {
            $key = self::FILE_PREFIX . md5($file->getPath());
            $usedKeys[] = $key;
            $cache->setItem($key, $file);
        }

        // remove any keys that are no longer used.
        $invalidatedKeys = array_diff($keys, $usedKeys);
        if ($invalidatedKeys) {
            $cache->removeItems($invalidatedKeys);
        }

        if ($cache instanceof OptimizableInterface) {
            $cache->optimize();
        }
    }

    /**
     * Removes all files in cache that do not occur in the given FileSet Collection.
     */
    public function garbageCollect($collection)
    {
//        $projectRoot = $collection->getProjectRoot();
//        $filenames = $collection->getFilenames();
//
//        foreach ($filenames as &$name) {
//            // the cache key contains a path relative to the project root; here we expect absolute paths.
//            $name = self::FILE_PREFIX . md5(substr($name, strlen($projectRoot)));
//        }
//
//        foreach ($this->getCache() as $item) {
//            if (substr($item, 0, strlen(self::FILE_PREFIX)) === self::FILE_PREFIX && !in_array($item, $filenames, true)) {
//                $this->getCache()->removeItem($item);
//            }
//        }
    }

    private function loadCacheItemAsFile(ProjectDescriptor $projectDescriptor, string $key)
    {
        $item = $this->getCache()->getItem($key);

        if ($item instanceof FileDescriptor) {
            $projectDescriptor->getFiles()->set($item->getPath(), $item);
        }
    }

    private function loadCacheItemAsSettings(ProjectDescriptor $projectDescriptor, string $key)
    {
        $item = $this->getCache()->getItem($key);

        if ($item instanceof Settings) {
            $projectDescriptor->setSettings($item);
        }
    }
}
