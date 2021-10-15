<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\GuideSetDescriptor;

final class ParseDirectoryCommand
{
    /** @var FilesystemInterface */
    private $origin;

    /** @var string */
    private $directory;

    /** @var GuideSetDescriptor */
    private $documentationSet;

    public function __construct(
        GuideSetDescriptor $documentationSet,
        FilesystemInterface $origin,
        string $directory
    ) {
        $this->origin = $origin;
        $this->directory = $directory;
        $this->documentationSet = $documentationSet;
    }

    public function getDocumentationSet(): GuideSetDescriptor
    {
        return $this->documentationSet;
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
