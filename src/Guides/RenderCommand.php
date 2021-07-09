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

    /** @var Configuration */
    private $configuration;

    public function __construct(
        Configuration $configuration,
        FilesystemInterface $filesystem
    ) {
        $this->filesystem = $filesystem;
        $this->configuration = $configuration;
    }

    public function getDestination() : FilesystemInterface
    {
        return $this->filesystem;
    }

    public function getConfiguration() : Configuration
    {
        return $this->configuration;
    }
}
