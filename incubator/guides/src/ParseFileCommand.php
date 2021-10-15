<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\GuideSetDescriptor;

final class ParseFileCommand
{
    /** @var string */
    private $directory;

    /** @var string */
    private $file;

    /** @var FilesystemInterface */
    private $origin;

    /** @var GuideSetDescriptor */
    private $documentationSet;

    public function __construct(
        GuideSetDescriptor $documentationSet,
        FilesystemInterface $origin,
        string $directory,
        string $file
    ) {
        $this->origin = $origin;
        $this->directory = $directory;
        $this->file = $file;
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

    public function getFile(): string
    {
        return $this->file;
    }
}
