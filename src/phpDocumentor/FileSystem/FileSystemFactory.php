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

namespace phpDocumentor\FileSystem;

use League\Flysystem\Filesystem;
use phpDocumentor\Dsn;

/**
 * Interface for FileSystem factories.
 */
interface FileSystemFactory
{
    /**
     * Returns a Filesystem instance based on the scheme of the provided Dsn.
     */
    public function create(Dsn $dsn): Filesystem;

    public function setOutputRoot(string $output);

    public function addVersion(string $versionNumber, string $folder);

    public function addDocumentationSet(string $versionNumber, array $source, string $output);
}
