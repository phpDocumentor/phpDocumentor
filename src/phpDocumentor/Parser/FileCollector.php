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

use phpDocumentor\Dsn;
use phpDocumentor\Reflection\File;

interface FileCollector
{
    /**
     * @param Dsn                  $dsn        dsn of source.
     * @param list<string>         $paths
     * @param array<string, mixed> $ignore     array containing keys 'paths' and 'hidden'
     * @param list<string>         $extensions
     *
     * @return File[]
     */
    public function getFiles(Dsn $dsn, array $paths, array $ignore, array $extensions) : array;
}
