<?php
/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-${YEAR} Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 *
 */

namespace phpDocumentor\Infrastructure\JmsSerializer;

use Metadata\Cache\CacheInterface;
use Metadata\ClassMetadata;

class FileCache implements CacheInterface
{
    /**
     * @var string
     */
    private $dir;

    /**
     * FileCache constructor.
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        if (!is_dir($dir)) {
            $this->createDirectory($dir);
        }

        $this->dir = rtrim($dir, '\\/');
    }

    /**
     * {@inheritDoc}
     */
    public function loadClassMetadataFromCache(\ReflectionClass $class)
    {
        $path = $this->dir.'/'.strtr($class->name, '\\', '-').'.cache.php';
        if (!file_exists($path)) {
            return null;
        }

        return include $path;
    }

    /**
     * {@inheritDoc}
     */
    public function putClassMetadataInCache(ClassMetadata $metadata)
    {
        $path = $this->getFileName($metadata);

        if (!is_writable(dirname($path))) {
            throw new \RuntimeException("Cache file {$path} is not writable.");
        }

        if (false === (@file_put_contents($path,
                '<?php return unserialize(' . var_export(serialize($metadata), true) . ');')
            )) {
            throw new \RuntimeException("Can't not write new cache file to {$path}");
        };

        // Let's not break filesystems which do not support chmod.
        @chmod($path, 0666 & ~umask());
    }

    /**
     * {@inheritDoc}
     */
    public function evictClassMetadataFromCache(\ReflectionClass $class)
    {
        $path = $this->dir.'/'.strtr($class->name, '\\', '-').'.cache.php';
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return string
     */
    private function getFileName(ClassMetadata $metadata)
    {
        return $this->dir . '/' . strtr($metadata->name, '\\', '-') . '.cache.php';
    }

    /**
     * @param $dir
     */
    private function createDirectory($dir)
    {
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException("Can't create directory for cache at {$dir}");
        }
    }
}
