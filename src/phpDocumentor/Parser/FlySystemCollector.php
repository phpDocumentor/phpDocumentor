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
use phpDocumentor\FileSystem\SpecificationFactoryInterface;

final class FlySystemCollector implements FileCollector
{
    public function __construct(
        private readonly SpecificationFactoryInterface $specificationFactory,
    ) {
    }

    /**
     * @param list<string|Path>    $paths
     * @param array<string, mixed> $ignore
     * @param list<string>         $extensions
     *
     * @return list<FlySystemFile>
     */
    public function getFiles(FileSystem $fileSystem, array $paths, array $ignore, array $extensions): array
    {
        $specs = $this->specificationFactory->create($paths, $ignore, $extensions);

        $files = [];

        foreach ($fileSystem->find($specs) as $file) {
            $files[] = new FlySystemFile($fileSystem, $file['path']);
        }

        return $files;
    }
}
