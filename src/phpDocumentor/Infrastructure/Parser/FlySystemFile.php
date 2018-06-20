<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Infrastructure\Parser;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Reflection\File;
use Webmozart\Assert\Assert;

/**
 * Wrapper for FlySystem's FilesystemInterface.
 */
final class FlySystemFile implements File
{
    /**
     * @var FilesystemInterface
     */
    private $fileSystem;

    /**
     * @var string
     */
    private $fileName;

    /**
     * FlySystemFile constructor.
     *
     * @param string $fileName
     */
    public function __construct(FilesystemInterface $fileSystem, $fileName)
    {
        Assert::string($fileName);
        $this->fileSystem = $fileSystem;
        $this->fileName = $fileName;
    }

    /**
     * Returns the content of the file as a string.
     *
     * @return string
     */
    public function getContents(): string
    {
        return $this->fileSystem->read($this->fileName);
    }

    /**
     * Returns md5 hash of the file.
     *
     * @return string
     */
    public function md5(): string
    {
        return md5($this->getContents());
    }

    /**
     * Returns an relative path to the file.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->fileName;
    }
}
