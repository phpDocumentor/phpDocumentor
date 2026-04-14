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
use phpDocumentor\FileSystem\Finder\Exclude;
use phpDocumentor\FileSystem\Finder\SpecificationFactoryInterface;
use phpDocumentor\FileSystem\Path;

final class FlySystemCollector implements FileCollector
{
    public function __construct(
        private readonly SpecificationFactoryInterface $specificationFactory,
    ) {
    }

    /**
     * @param list<string|Path>    $paths
     * @param list<string>         $extensions
     *
     * @return list<FlySystemFile>
     */
    public function getFiles(FileSystem $fileSystem, array $paths, Exclude $exclude, array $extensions): array
    {
        $specs = $this->specificationFactory->create(
            $paths,
            $exclude,
            $extensions,
        );

        $files = [];

        foreach ($fileSystem->find($specs) as $file) {
            $files[] = new FlySystemFile($fileSystem, $file['path']);
        }

        return $files;
    }
}
