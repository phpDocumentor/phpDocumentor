<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Cache;

use Zend\Cache\Storage\IterableInterface;
use Zend\Cache\Storage\IteratorInterface;
use Zend\Cache\Storage\OptimizableInterface;
use Zend\Cache\Storage\StorageInterface;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Fileset\Collection;

/**
 * Maps a projectDescriptor to and from a cache instance.
 */
class ProjectDescriptorMapper
{
    const FILE_PREFIX = 'file_';

    const KEY_SETTINGS = 'settings';

    /** @var StorageInterface|IterableInterface */
    protected $cache;

    /**
     * Initializes this mapper with the given cache instance.
     * @param StorageInterface $cache
     */
    public function __construct(StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Returns the Cache instance for this Mapper.
     *
     * @return IterableInterface|StorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Returns the Project Descriptor from the cache.
     *
     * @param ProjectDescriptor $projectDescriptor
     *
     * @return void
     */
    public function populate(ProjectDescriptor $projectDescriptor)
    {
        /** @var IteratorInterface $iteratorInterface */
        $iteratorInterface = $this->getCache()->getIterator();

        // load the settings object
        try {
            $settings = $this->getCache()->getItem(self::KEY_SETTINGS);
        } catch (\Exception $e) {
            $settings = $this->igBinaryCompatibleCacheClear(self::KEY_SETTINGS, $e);
        }

        if ($settings) {
            $projectDescriptor->setSettings($settings);
        }

        // FIXME: Workaround for: https://github.com/zendframework/zf2/pull/4154
        if ($iteratorInterface->valid()) {
            foreach ($this->getCache() as $key) {
                try {
                    $item = $this->getCache()->getItem($key);
                } catch (\Exception $e) {
                    $this->igBinaryCompatibleCacheClear($key, $e);
                }

                if ($item instanceof FileDescriptor) {
                    $projectDescriptor->getFiles()->set($item->getPath(), $item);
                }
            }
        }
    }

    /**
     * Clears the cache if a serialization exception was thrown
     *
     * @param string $key
     * @param \Exception $e
     *
     * @throws \Exception Rethrows exception if nessesary
     *
     * @return void
     */
    protected function igBinaryCompatibleCacheClear($key, $e)
    {
        if (extension_loaded('igbinary')) {
            $this->getCache()->removeItem($key);
        } else {
            throw $e;
        }
    }

    /**
     * Stores a Project Descriptor in the Cache.
     *
     * @param ProjectDescriptor $projectDescriptor
     *
     * @return void
     */
    public function save(ProjectDescriptor $projectDescriptor)
    {
        $keys  = array();
        $cache = $this->getCache();

        /** @var IteratorInterface $iteratorInterface  */
        $iteratorInterface = $cache->getIterator();

        // FIXME: Workaround for: https://github.com/zendframework/zf2/pull/4154
        if ($iteratorInterface->valid()) {
            foreach ($cache as $key) {
                $keys[] = $key;
            }
        }

        // store the settings for this Project Descriptor
        $cache->setItem(self::KEY_SETTINGS, $projectDescriptor->getSettings());

        // store cache items
        $usedKeys = array(self::KEY_SETTINGS);
        foreach ($projectDescriptor->getFiles() as $file) {
            $key        = self::FILE_PREFIX . md5($file->getPath());
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
     *
     * @param Collection $collection
     *
     * @return void
     */
    public function garbageCollect(Collection $collection)
    {
        $projectRoot = $collection->getProjectRoot();
        $filenames   = $collection->getFilenames();

        foreach ($filenames as &$name) {
            // the cache key contains a path relative to the project root; here we expect absolute paths.
            $name = self::FILE_PREFIX . md5(substr($name, strlen($projectRoot)));
        }

        /** @var IteratorInterface $iteratorInterface  */
        $iteratorInterface = $this->getCache()->getIterator();

        // FIXME: Workaround for: https://github.com/zendframework/zf2/pull/4154
        if ($iteratorInterface->valid()) {
            foreach ($this->getCache() as $item) {
                if (substr($item, 0, strlen(self::FILE_PREFIX)) === self::FILE_PREFIX && !in_array($item, $filenames)) {
                    $this->getCache()->removeItem($item);
                }
            }
        }
    }
}
