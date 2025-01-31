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

use phpDocumentor\FileSystem\FileSystem;
use phpDocumentor\FileSystem\Path;
use phpDocumentor\Reflection\File;

interface FileCollector
{
    /**
     * @param list<string|Path> $paths
     * @param array{paths: string[], hidden: bool} $ignore     array containing keys 'paths' and 'hidden'
     * @param list<string> $extensions
     *
     * @return File[]
     */
    public function getFiles(FileSystem $fileSystem, array $paths, array $ignore, array $extensions): array;
}
