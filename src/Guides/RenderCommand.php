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
use phpDocumentor\Descriptor\GuideSetDescriptor;

final class RenderCommand
{
    /** @var FilesystemInterface */
    private $filesystem;

    /** @var Configuration */
    private $configuration;

    /** @var GuideSetDescriptor */
    private $documentationSet;

    public function __construct(
        GuideSetDescriptor $documentationSet,
        Configuration $configuration,
        FilesystemInterface $filesystem
    ) {
        $this->filesystem = $filesystem;
        $this->configuration = $configuration;
        $this->documentationSet = $documentationSet;
    }

    public function getDocumentationSet(): GuideSetDescriptor
    {
        return $this->documentationSet;
    }

    public function getDestination(): FilesystemInterface
    {
        return $this->filesystem;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }
}
