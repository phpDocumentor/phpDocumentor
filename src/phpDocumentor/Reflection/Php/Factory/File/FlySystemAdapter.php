<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory\File;

use League\Flysystem\FilesystemInterface;

final class FlySystemAdapter implements Adapter
{
    /**
     * Filesystem used to read file.
     *
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * Initializes the object with a FileSystem.
     *
     * @param FilesystemInterface $filesystem
     */
    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Returns true when the file exists.
     *
     * @param string $filePath
     * @return boolean
     */
    public function fileExists($filePath)
    {
        return $this->filesystem->has($filePath);
    }

    /**
     * Returns the content of the file as a string.
     *
     * @param $filePath
     * @return string
     */
    public function getContents($filePath)
    {
        return $this->filesystem->read($filePath);
    }

    /**
     * Returns md5 hash of the file.
     *
     * @param $filePath
     * @return string
     */
    public function md5($filePath)
    {
        return md5($this->getContents($filePath));
    }

    /**
     * Returns an relative path to the file.
     *
     * @param string $filePath
     * @return string
     */
    public function path($filePath)
    {
        return $filePath;
    }
}

