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

namespace phpDocumentor\Parser;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Reflection\File;
use function md5;

final class FlySystemFile implements File
{
    /** @var FilesystemInterface */
    private $fileSystem;

    /** @var string */
    private $fileName;

    public function __construct(FilesystemInterface $fileSystem, string $fileName)
    {
        $this->fileSystem = $fileSystem;
        $this->fileName   = $fileName;
    }

    /**
     * Returns the content of the file as a string.
     *
     * @throws FileNotFoundException
     */
    public function getContents() : string
    {
        return $this->fileSystem->read($this->fileName);
    }

    /**
     * Returns md5 hash of the file.
     *
     * @throws FileNotFoundException
     */
    public function md5() : string
    {
        return md5($this->getContents());
    }

    /**
     * Returns an relative path to the file.
     */
    public function path() : string
    {
        return $this->fileName;
    }
}
