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
use phpDocumentor\Path;

final class FlySystemCollector implements FileCollector
{
    /** @var SpecificationFactoryInterface */
    private $specificationFactory;

    /** @var FlySystemFactory */
    private $flySystemFactory;

    public function __construct(SpecificationFactoryInterface $specificationFactory, FlySystemFactory $flySystemFactory)
    {
        $this->specificationFactory = $specificationFactory;
        $this->flySystemFactory     = $flySystemFactory;
    }

    /**
     * @param list<string|Path>    $paths
     * @param array<string, mixed> $ignore
     * @param list<string>         $extensions
     *
     * @return list<FlySystemFile>
     */
    public function getFiles(Dsn $dsn, array $paths, array $ignore, array $extensions): array
    {
        $specs = $this->specificationFactory->create($paths, $ignore, $extensions);

        $fileSystem = $this->flySystemFactory->create($dsn);

        $files = [];

        foreach ($fileSystem->find($specs) as $file) {
            $files[] = new FlySystemFile($fileSystem, $file['path']);
        }

        return $files;
    }
}
