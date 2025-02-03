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

use phpDocumentor\FileSystem\FileNotFoundException;
use phpDocumentor\FileSystem\FileSystem;
use phpDocumentor\Reflection\File;
use Webmozart\Assert\Assert;

use function md5;

final class FlySystemFile implements File
{
    public function __construct(private readonly FileSystem $fileSystem, private readonly string $fileName)
    {
    }

    /**
     * Returns the content of the file as a string.
     *
     * @throws FileNotFoundException
     */
    public function getContents(): string
    {
        $contents = $this->fileSystem->read($this->fileName);

        Assert::notFalse($contents);

        return $contents;
    }

    /**
     * Returns md5 hash of the file.
     *
     * @throws FileNotFoundException
     */
    public function md5(): string
    {
        return md5($this->getContents());
    }

    /**
     * Returns an relative path to the file.
     */
    public function path(): string
    {
        return $this->fileName;
    }
}
