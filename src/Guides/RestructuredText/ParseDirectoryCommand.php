<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Guides\Configuration;

final class ParseDirectoryCommand
{
    /** @var Configuration */
    private $configuration;

    /** @var FilesystemInterface */
    private $origin;

    /** @var string */
    private $directory;

    /** @var GuideSetDescriptor */
    private $documentationSet;

    public function __construct(
        GuideSetDescriptor $documentationSet,
        Configuration $configuration,
        FilesystemInterface $origin,
        string $directory
    ) {
        $this->configuration = $configuration;
        $this->origin = $origin;
        $this->directory = $directory;
        $this->documentationSet = $documentationSet;
    }

    public function getDocumentationSet(): GuideSetDescriptor
    {
        return $this->documentationSet;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getOrigin(): FilesystemInterface
    {
        return $this->origin;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }
}
