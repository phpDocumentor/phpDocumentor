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

namespace phpDocumentor\Guides;

use League\Flysystem\FilesystemInterface;

final class RenderCommand
{
    /** @var FilesystemInterface */
    private $filesystem;

    /** @var string */
    private $outputDirectory;

    public function __construct(FilesystemInterface $filesystem, string $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
        $this->filesystem = $filesystem;
    }

    public function getDestination(): FilesystemInterface
    {
        return $this->filesystem;
    }

    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }
}
